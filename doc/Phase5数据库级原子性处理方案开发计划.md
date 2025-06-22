# Phase 5 数据库级原子性处理方案开发计划

## 📋 **项目概述**

**阶段**: Phase 5 - 数据库级原子性处理方案  
**计划开始**: 2024年12月19日  
**预计完成**: 2024年12月22日 (3-4天)  
**开发目标**: 解决现有守护进程并发冲突问题，实现真正可靠的自动派单系统  

---

## 🎯 **核心问题分析**

### **当前系统问题**
基于深度分析和实际运行情况发现：

1. **并发冲突根本原因**：
   - 守护进程使用嵌套事务（processCoolingOrders → do_order）
   - 缺乏原子性的状态检查和更新机制
   - 数据库进程卡死：`UPDATE xy_users SET deal_status = 3 WHERE id = 1`

2. **状态切换安全问题**：
   - 自动派单↔手动派单切换时可能与正在处理的订单冲突
   - 缺乏版本控制机制防止并发修改

3. **系统脆弱性**：
   - 用户访问后台页面时可能触发状态更新导致冲突
   - 守护进程崩溃后需要手动重启

---

## 🚀 **解决方案设计**

### **核心思路**
完全基于数据库的原子性处理，消除应用层并发问题：

1. **数据库存储过程** - 确保原子性操作
2. **版本控制机制** - 防止并发冲突
3. **MySQL事件调度器** - 替代应用层守护进程
4. **乐观锁设计** - 安全的状态切换

---

## 📅 **开发时间线**

| 子阶段 | 时间 | 主要任务 | 预期产出 |
|--------|------|----------|----------|
| **5.1** | 0.5天 | 数据库结构调整 | 版本字段、日志表 |
| **5.2** | 1.5天 | 存储过程开发 | 原子性处理逻辑 |
| **5.3** | 0.5天 | 事件调度器配置 | 定时任务替换 |
| **5.4** | 0.5天 | 应用层适配 | 状态切换安全化 |
| **5.5** | 1天 | 测试验证优化 | 完整测试报告 |

---

## 🔧 **Phase 5.1: 数据库结构调整 (0.5天)**

### **5.1.1 添加版本控制字段**

```sql
-- 订单表添加版本字段
ALTER TABLE xy_convey ADD COLUMN version INT DEFAULT 1 COMMENT '版本号，用于乐观锁';

-- 用户表添加版本字段（如需要）
ALTER TABLE xy_users ADD COLUMN version INT DEFAULT 1 COMMENT '版本号，用于乐观锁';
```

### **5.1.2 创建自动派单日志表**

```sql
CREATE TABLE xy_auto_dispatch_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(20) COMMENT '订单ID',
    user_id INT COMMENT '用户ID',
    amount DECIMAL(10,2) COMMENT '订单金额',
    commission DECIMAL(10,2) COMMENT '佣金金额',
    status VARCHAR(20) COMMENT '处理状态: success/error/insufficient_balance',
    error_msg TEXT COMMENT '错误信息',
    execution_time INT COMMENT '执行耗时(毫秒)',
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    INDEX idx_order_id (order_id),
    INDEX idx_create_time (create_time),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自动派单处理日志';
```

### **5.1.3 创建系统监控表**

```sql
CREATE TABLE xy_dispatch_monitor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    check_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    found_orders INT DEFAULT 0 COMMENT '发现待处理订单数',
    processed_orders INT DEFAULT 0 COMMENT '成功处理订单数',
    failed_orders INT DEFAULT 0 COMMENT '失败订单数',
    execution_time INT COMMENT '执行耗时(毫秒)',
    INDEX idx_check_time (check_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自动派单监控记录';
```

---

## 🔧 **Phase 5.2: 存储过程开发 (1.5天)**

### **5.2.1 单订单原子性处理存储过程**

```sql
DELIMITER $$

CREATE PROCEDURE ProcessAutoDispatchOrder(IN order_id VARCHAR(20))
BEGIN
    DECLARE v_user_id INT;
    DECLARE v_amount DECIMAL(10,2);
    DECLARE v_commission DECIMAL(10,2);
    DECLARE v_balance DECIMAL(10,2);
    DECLARE v_version INT;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE v_start_time BIGINT;
    DECLARE v_execution_time INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        GET DIAGNOSTICS CONDITION 1
        @sqlstate = RETURNED_SQLSTATE, @errno = MYSQL_ERRNO, @text = MESSAGE_TEXT;
        
        INSERT INTO xy_auto_dispatch_log (order_id, status, error_msg, execution_time, create_time) 
        VALUES (order_id, 'error', CONCAT('SQL异常 ', @errno, ': ', @text), 
                ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time), NOW());
    END;
    
    SET v_start_time = UNIX_TIMESTAMP(NOW(3)) * 1000;
    
    START TRANSACTION;
    
    -- 原子性检查并锁定订单（关键：使用版本号防止并发）
    SELECT uid, num, commission, version INTO v_user_id, v_amount, v_commission, v_version
    FROM xy_convey 
    WHERE id = order_id 
    AND auto_dispatch = 1 
    AND dispatch_status = 0 
    AND cooling_end_time > 0
    AND cooling_end_time <= UNIX_TIMESTAMP() 
    AND status = 0
    FOR UPDATE;
    
    -- 如果没有找到符合条件的订单，说明已被处理或状态已变更
    IF v_user_id IS NULL THEN
        ROLLBACK;
        LEAVE;
    END IF;
    
    -- 标记订单为处理中，同时更新版本号（防止其他进程处理）
    UPDATE xy_convey 
    SET dispatch_status = 999, version = version + 1 
    WHERE id = order_id AND version = v_version;
    
    SET v_affected_rows = ROW_COUNT();
    
    -- 如果更新失败，说明版本号已变更（被其他进程处理）
    IF v_affected_rows = 0 THEN
        ROLLBACK;
        INSERT INTO xy_auto_dispatch_log (order_id, status, error_msg, execution_time, create_time) 
        VALUES (order_id, 'skipped', '订单已被其他进程处理', 
                ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time), NOW());
        LEAVE;
    END IF;
    
    -- 检查用户余额
    SELECT balance INTO v_balance FROM xy_users WHERE id = v_user_id FOR UPDATE;
    
    IF v_balance >= v_amount THEN
        -- 执行付款：扣除余额，增加冻结余额
        UPDATE xy_users 
        SET balance = balance - v_amount,
            freeze_balance = freeze_balance + v_amount + v_commission
        WHERE id = v_user_id;
        
        -- 立即结算：返还本金+佣金，减少冻结余额
        UPDATE xy_users 
        SET balance = balance + v_amount + v_commission,
            freeze_balance = freeze_balance - v_amount - v_commission
        WHERE id = v_user_id;
        
        -- 更新订单为最终完成状态
        UPDATE xy_convey 
        SET status = 1, dispatch_status = 1, c_status = 1, 
            endtime = UNIX_TIMESTAMP(), version = version + 1
        WHERE id = order_id;
        
        -- 记录交易日志
        INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
        VALUES (v_user_id, order_id, v_amount, 2, 2, UNIX_TIMESTAMP());
        
        -- 记录佣金日志
        INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
        VALUES (v_user_id, order_id, v_commission, 3, 1, UNIX_TIMESTAMP());
        
        -- 记录奖励日志
        INSERT INTO xy_reward_log (oid, uid, num, addtime, type) 
        VALUES (order_id, v_user_id, v_amount, UNIX_TIMESTAMP(), 2);
        
        COMMIT;
        
        SET v_execution_time = ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time);
        
        -- 记录成功日志
        INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, execution_time, create_time) 
        VALUES (order_id, v_user_id, v_amount, v_commission, 'success', v_execution_time, NOW());
        
    ELSE
        -- 余额不足，恢复状态
        UPDATE xy_convey 
        SET dispatch_status = 0, version = version + 1 
        WHERE id = order_id;
        
        COMMIT;
        
        SET v_execution_time = ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time);
        
        -- 记录余额不足日志
        INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, error_msg, execution_time, create_time) 
        VALUES (order_id, v_user_id, v_amount, v_commission, 'insufficient_balance', 
               CONCAT('需要:', v_amount, ', 余额:', v_balance), v_execution_time, NOW());
    END IF;
    
END$$

DELIMITER ;
```

### **5.2.2 批量处理存储过程**

```sql
DELIMITER $$

CREATE PROCEDURE ProcessAllExpiredOrders()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_order_id VARCHAR(20);
    DECLARE v_found_count INT DEFAULT 0;
    DECLARE v_processed_count INT DEFAULT 0;
    DECLARE v_failed_count INT DEFAULT 0;
    DECLARE v_start_time BIGINT;
    DECLARE v_execution_time INT;
    
    DECLARE order_cursor CURSOR FOR 
        SELECT id FROM xy_convey 
        WHERE auto_dispatch = 1 
        AND dispatch_status = 0 
        AND cooling_end_time > 0 
        AND cooling_end_time <= UNIX_TIMESTAMP() 
        AND status = 0;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    SET v_start_time = UNIX_TIMESTAMP(NOW(3)) * 1000;
    
    -- 统计待处理订单数量
    SELECT COUNT(*) INTO v_found_count FROM xy_convey 
    WHERE auto_dispatch = 1 
    AND dispatch_status = 0 
    AND cooling_end_time > 0 
    AND cooling_end_time <= UNIX_TIMESTAMP() 
    AND status = 0;
    
    OPEN order_cursor;
    
    process_loop: LOOP
        FETCH order_cursor INTO v_order_id;
        IF done THEN
            LEAVE process_loop;
        END IF;
        
        -- 处理每个订单（独立事务）
        CALL ProcessAutoDispatchOrder(v_order_id);
        
        -- 检查处理结果
        IF (SELECT COUNT(*) FROM xy_auto_dispatch_log 
            WHERE order_id = v_order_id AND status = 'success' 
            AND create_time >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)) > 0 THEN
            SET v_processed_count = v_processed_count + 1;
        ELSE
            SET v_failed_count = v_failed_count + 1;
        END IF;
        
    END LOOP;
    
    CLOSE order_cursor;
    
    SET v_execution_time = ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time);
    
    -- 记录监控数据
    INSERT INTO xy_dispatch_monitor (found_orders, processed_orders, failed_orders, execution_time) 
    VALUES (v_found_count, v_processed_count, v_failed_count, v_execution_time);
    
END$$

DELIMITER ;
```

---

## 🔧 **Phase 5.3: 事件调度器配置 (0.5天)**

### **5.3.1 启用MySQL事件调度器**

```sql
-- 检查事件调度器状态
SHOW VARIABLES LIKE 'event_scheduler';

-- 启用事件调度器
SET GLOBAL event_scheduler = ON;

-- 永久启用（在my.cnf中添加）
-- event_scheduler = ON
```

### **5.3.2 创建定时事件**

```sql
-- 创建每分钟执行的自动派单事件
CREATE EVENT IF NOT EXISTS auto_dispatch_event
ON SCHEDULE EVERY 1 MINUTE
STARTS CURRENT_TIMESTAMP
COMMENT '自动派单处理事件'
DO CALL ProcessAllExpiredOrders();

-- 启用事件
ALTER EVENT auto_dispatch_event ENABLE;
```

### **5.3.3 创建监控和清理事件**

```sql
-- 创建日志清理事件（每天清理7天前的日志）
CREATE EVENT IF NOT EXISTS cleanup_dispatch_logs
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
COMMENT '清理自动派单日志'
DO 
BEGIN
    DELETE FROM xy_auto_dispatch_log WHERE create_time < DATE_SUB(NOW(), INTERVAL 7 DAY);
    DELETE FROM xy_dispatch_monitor WHERE check_time < DATE_SUB(NOW(), INTERVAL 7 DAY);
END;

ALTER EVENT cleanup_dispatch_logs ENABLE;
```

---

## 🔧 **Phase 5.4: 应用层适配 (0.5天)**

### **5.4.1 修改状态切换方法**

**文件**: `application/admin/model/Convey.php`

```php
/**
 * 安全切换到自动派单模式（带版本控制）
 */
public function switchToAutoDispatch($orderId)
{
    return Db::transaction(function() use ($orderId) {
        $order = Db::name('xy_convey')->where('id', $orderId)->lock(true)->find();
        
        if (!$order || $order['status'] != 0) {
            throw new \Exception('订单状态不允许切换');
        }
        
        // 检查是否正在处理中
        if ($order['dispatch_status'] == 999) {
            throw new \Exception('订单正在处理中，请稍后再试');
        }
        
        $coolingPeriod = get_dispatch_config('cooling_period_minutes', 1) * 60;
        $updateData = [
            'auto_dispatch' => 1,
            'manual_dispatch' => 0,
            'dispatch_status' => 0,
            'cooling_end_time' => time() + $coolingPeriod,
            'version' => $order['version'] + 1
        ];
        
        $result = Db::name('xy_convey')
            ->where('id', $orderId)
            ->where('version', $order['version'])
            ->update($updateData);
        
        if (!$result) {
            throw new \Exception('切换失败，订单状态已变更，请刷新后重试');
        }
        
        return ['code' => 0, 'info' => '已切换为自动派单模式'];
    });
}

/**
 * 安全切换到手动派单模式（带版本控制）
 */
public function switchToManualDispatch($orderId)
{
    return Db::transaction(function() use ($orderId) {
        $order = Db::name('xy_convey')->where('id', $orderId)->lock(true)->find();
        
        if (!$order || $order['status'] != 0) {
            throw new \Exception('订单状态不允许切换');
        }
        
        // 检查是否正在处理中
        if ($order['dispatch_status'] == 999) {
            throw new \Exception('订单正在处理中，请稍后再试');
        }
        
        $updateData = [
            'auto_dispatch' => 0,
            'manual_dispatch' => 1,
            'dispatch_status' => 0,
            'cooling_end_time' => 0,
            'version' => $order['version'] + 1
        ];
        
        $result = Db::name('xy_convey')
            ->where('id', $orderId)
            ->where('version', $order['version'])
            ->update($updateData);
        
        if (!$result) {
            throw new \Exception('切换失败，订单状态已变更，请刷新后重试');
        }
        
        return ['code' => 0, 'info' => '已切换为手动派单模式'];
    });
}
```

### **5.4.2 停用现有守护进程**

**文件**: `application/index/controller/Daemon.php`

```php
/**
 * 停用守护进程（由数据库事件调度器替代）
 */
public function auto_dispatch()
{
    echo "=== 守护进程已停用 ===\n";
    echo "自动派单现已由MySQL事件调度器处理\n";
    echo "查看处理日志: SELECT * FROM xy_auto_dispatch_log ORDER BY create_time DESC LIMIT 10;\n";
    echo "查看监控数据: SELECT * FROM xy_dispatch_monitor ORDER BY check_time DESC LIMIT 10;\n";
    return;
}
```

### **5.4.3 添加监控接口**

**文件**: `application/admin/controller/Deal.php`

```php
/**
 * 自动派单监控页面
 * @auth true
 * @menu true
 */
public function auto_dispatch_monitor()
{
    $this->title = '自动派单监控';
    
    // 获取最近24小时的监控数据
    $monitors = Db::name('xy_dispatch_monitor')
        ->where('check_time', '>=', date('Y-m-d H:i:s', time() - 86400))
        ->order('check_time DESC')
        ->paginate(20);
    
    // 获取最近的处理日志
    $logs = Db::name('xy_auto_dispatch_log')
        ->where('create_time', '>=', date('Y-m-d H:i:s', time() - 86400))
        ->order('create_time DESC')
        ->limit(50)
        ->select();
    
    // 统计数据
    $stats = [
        'total_processed' => Db::name('xy_auto_dispatch_log')->where('status', 'success')->count(),
        'total_failed' => Db::name('xy_auto_dispatch_log')->where('status', '!=', 'success')->count(),
        'today_processed' => Db::name('xy_auto_dispatch_log')
            ->where('status', 'success')
            ->where('create_time', '>=', date('Y-m-d 00:00:00'))
            ->count(),
        'pending_orders' => Db::name('xy_convey')
            ->where('auto_dispatch', 1)
            ->where('dispatch_status', 0)
            ->where('cooling_end_time', '>', 0)
            ->where('cooling_end_time', '<=', time())
            ->where('status', 0)
            ->count()
    ];
    
    $this->assign('monitors', $monitors);
    $this->assign('logs', $logs);
    $this->assign('stats', $stats);
    
    return $this->fetch();
}

/**
 * 手动触发自动派单检查
 * @auth true
 */
public function trigger_auto_dispatch()
{
    try {
        Db::execute('CALL ProcessAllExpiredOrders()');
        return $this->success('手动触发成功，请查看监控页面');
    } catch (\Exception $e) {
        return $this->error('触发失败: ' . $e->getMessage());
    }
}
```

---

## 🔧 **Phase 5.5: 测试验证优化 (1天)**

### **5.5.1 功能测试清单**

#### **原子性测试**
- [ ] 同时创建多个到期订单，验证无重复处理
- [ ] 模拟并发状态切换，验证版本控制有效性
- [ ] 测试余额不足情况的正确处理

#### **性能测试**
- [ ] 大批量订单处理性能测试
- [ ] 数据库锁等待时间测试
- [ ] 存储过程执行效率测试

#### **异常处理测试**
- [ ] 数据库连接中断恢复测试
- [ ] 存储过程异常回滚测试
- [ ] 事件调度器重启恢复测试

### **5.5.2 监控验证**

#### **监控指标**
- 处理成功率 > 99%
- 平均处理时间 < 100ms
- 并发冲突率 = 0%
- 数据一致性 = 100%

#### **日志分析**
- 成功处理日志完整性
- 错误日志详细程度
- 性能指标记录准确性

### **5.5.3 压力测试**

#### **测试场景**
- 1000个订单同时到期处理
- 高频状态切换操作
- 长时间运行稳定性测试

---

## 📊 **预期成果**

### **技术成果**
1. **完全消除并发冲突** - 基于数据库级原子性操作
2. **高可靠性** - 事件调度器自动恢复，无需人工干预
3. **高性能** - 数据库级处理，避免应用层开销
4. **完善监控** - 实时监控和历史数据分析
5. **安全切换** - 版本控制确保状态切换安全

### **业务成果**
1. **用户体验提升** - 自动派单稳定可靠
2. **运维成本降低** - 无需维护守护进程
3. **系统稳定性** - 消除因并发导致的系统问题
4. **可扩展性** - 支持更大规模的订单处理

---

## ⚠️ **风险评估**

### **高风险项**
1. **MySQL事件调度器依赖** - 需要确保MySQL配置正确
2. **存储过程复杂性** - 调试和维护相对复杂
3. **数据库权限** - 需要CREATE EVENT等高级权限

### **缓解措施**
1. **备用方案** - 保留HTTP接口作为备用触发方式
2. **详细文档** - 完整的存储过程说明和维护文档
3. **权限检查** - 部署前确认数据库权限配置

---

## 🚀 **部署计划**

### **部署步骤**
1. **数据库备份** - 完整备份现有数据
2. **结构调整** - 执行DDL脚本添加字段和表
3. **存储过程部署** - 创建所有存储过程
4. **事件调度器配置** - 启用并配置定时事件
5. **应用代码部署** - 更新应用层代码
6. **功能验证** - 完整的功能测试验证

### **回滚方案**
1. **禁用事件调度器** - 快速停止自动处理
2. **恢复守护进程** - 启用原有守护进程
3. **数据回滚** - 必要时恢复数据库备份

---

## 📈 **成功指标**

| 指标 | 目标值 | 验证方法 |
|------|--------|----------|
| 并发冲突率 | 0% | 并发测试验证 |
| 处理成功率 | >99.9% | 监控数据统计 |
| 平均处理时间 | <100ms | 性能测试数据 |
| 系统稳定性 | 7×24小时无故障 | 长期运行测试 |
| 数据一致性 | 100% | 数据校验脚本 |

---

## 📝 **文档交付**

### **技术文档**
- [ ] 存储过程详细说明
- [ ] 事件调度器配置指南
- [ ] 监控使用手册
- [ ] 故障排查指南

### **运维文档**
- [ ] 部署操作手册
- [ ] 备份恢复流程
- [ ] 性能调优指南
- [ ] 常见问题解答

---

## 🎉 **Phase 5 总结预期**

**Phase 5 完成后将实现**:
- ✅ 彻底解决并发冲突问题
- ✅ 建立高可靠的自动派单系统
- ✅ 实现真正的7×24小时无人值守运行
- ✅ 提供完善的监控和故障排查能力
- ✅ 为系统长期稳定运行奠定坚实基础

**这将是整个派单系统开发的关键里程碑！**

---

**计划制定时间**: 2024年12月19日  
**计划版本**: v1.0  
**制定依据**: 深度源码分析 + 实际运行问题 + 技术方案讨论  
**下一步**: 等待确认后开始Phase 5.1实施 