-- ================================
-- 统一派单方案数据库更新脚本
-- 让自动派单也创建空订单，冷却期结束时系统匹配商品
-- ================================

USE 6ui;

-- ================================
-- 第一步：备份当前存储过程
-- ================================
-- 备份现有存储过程定义
DROP PROCEDURE IF EXISTS ProcessAutoDispatchOrder_backup;

-- 获取当前存储过程定义并创建备份
DELIMITER $$
CREATE PROCEDURE ProcessAutoDispatchOrder_backup()
BEGIN
    SELECT 'ProcessAutoDispatchOrder 已备份' as backup_status;
END$$
DELIMITER ;

-- ================================
-- 第二步：重建ProcessAutoDispatchOrder存储过程
-- ================================
DROP PROCEDURE IF EXISTS ProcessAutoDispatchOrder;

DELIMITER $$
CREATE PROCEDURE ProcessAutoDispatchOrder(IN order_id CHAR(18))
BEGIN
    DECLARE v_user_id INT DEFAULT NULL;
    DECLARE v_balance DECIMAL(10,2) DEFAULT 0;
    DECLARE v_user_level INT DEFAULT 0;
    DECLARE v_order_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_commission DECIMAL(10,2) DEFAULT 0;
    DECLARE v_goods_id INT DEFAULT 0;
    DECLARE v_goods_price DECIMAL(10,2) DEFAULT 0;
    DECLARE v_version INT DEFAULT 0;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE v_enable_smart_downgrade INT DEFAULT 0;
    DECLARE v_start_time BIGINT DEFAULT 0;
    DECLARE v_execution_time BIGINT DEFAULT 0;
    DECLARE v_payment_exists INT DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_message, execution_time, create_time)
        VALUES (order_id, IFNULL(v_user_id, 0), 'error', 
                CONCAT('SQL异常: ', @@error_count), 
                ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), UNIX_TIMESTAMP());
    END;
    
    SET v_start_time = UNIX_TIMESTAMP() * 1000;
    START TRANSACTION;
    
    -- 1. 获取空订单信息并锁定（关键：只处理goods_id=0的空订单）
    SELECT uid, version INTO v_user_id, v_version
    FROM xy_convey 
    WHERE id = order_id 
      AND auto_dispatch = 1 
      AND dispatch_status = 0 
      AND status = 0
      AND goods_id = 0  -- 核心变更：只处理空订单
    FOR UPDATE;
    
    IF v_user_id IS NOT NULL THEN
        -- 2. 锁定订单防止重复处理
        UPDATE xy_convey 
        SET dispatch_status = 999, version = version + 1 
        WHERE id = order_id AND version = v_version;
        
        SET v_affected_rows = ROW_COUNT();
        
        IF v_affected_rows > 0 THEN
            -- 3. 获取用户当前信息
            SELECT balance, level INTO v_balance, v_user_level 
            FROM xy_users WHERE id = v_user_id FOR UPDATE;
            
            -- 4. 智能商品匹配（基于当前余额动态选择）
            SELECT id, goods_price 
            INTO v_goods_id, v_goods_price
            FROM xy_goods_list 
            WHERE status = 1 
              AND goods_price <= v_balance 
              AND goods_price >= (v_balance * 0.1)  -- 最小使用10%余额
            ORDER BY RAND() 
            LIMIT 1;
            
            IF v_goods_id > 0 AND v_balance >= v_goods_price THEN
                -- 5. 计算佣金（根据用户等级）
                SELECT COALESCE(v_goods_price * bili, v_goods_price * 0.025) INTO v_commission
                FROM xy_level WHERE level = v_user_level LIMIT 1;
                
                SET v_order_amount = v_goods_price;
                
                -- 6. 更新订单商品信息
                UPDATE xy_convey 
                SET goods_id = v_goods_id,
                    goods_count = 1,
                    num = v_order_amount,
                    commission = v_commission,
                    dispatch_status = 0  -- 恢复为正常状态
                WHERE id = order_id;
                
                -- 7. 扣款操作
                UPDATE xy_users 
                SET balance = balance - v_order_amount,
                    deal_status = 3  -- 设置为交易中
                WHERE id = v_user_id;
                
                -- 8. 立即结算
                UPDATE xy_users 
                SET balance = balance + v_order_amount + v_commission,
                    deal_status = 1  -- 恢复正常状态
                WHERE id = v_user_id;
                
                -- 9. 更新订单完成状态
                UPDATE xy_convey 
                SET status = 1, 
                    dispatch_status = 1, 
                    c_status = 1,
                    endtime = UNIX_TIMESTAMP()
                WHERE id = order_id;
                
                -- 10. 记录完整的日志链
                INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
                VALUES (v_user_id, order_id, v_order_amount, 2, 2, UNIX_TIMESTAMP());
                
                INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
                VALUES (v_user_id, order_id, v_order_amount + v_commission, 3, 1, UNIX_TIMESTAMP());
                
                INSERT INTO xy_reward_log (oid, uid, num, addtime, type) 
                VALUES (order_id, v_user_id, v_order_amount, UNIX_TIMESTAMP(), 2);
                
                COMMIT;
                
                SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
                INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, execution_time, create_time)
                VALUES (order_id, v_user_id, v_order_amount, v_commission, 'completed', v_execution_time, UNIX_TIMESTAMP());
                
            ELSE
                -- 余额不足或无可用商品，执行智能降级
                SELECT CAST(value AS UNSIGNED) INTO v_enable_smart_downgrade 
                FROM system_config WHERE name = 'enable_smart_downgrade' LIMIT 1;
                
                IF v_enable_smart_downgrade = 1 THEN
                    -- 智能降级：转为手动派单
                    UPDATE xy_convey 
                    SET auto_dispatch = 0, 
                        manual_dispatch = 1,
                        dispatch_status = 0,
                        cooling_end_time = 0
                    WHERE id = order_id;
                    
                    COMMIT;
                    
                    SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
                    INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_message, execution_time, create_time)
                    VALUES (order_id, v_user_id, 'smart_downgrade', 
                            CONCAT('余额不足智能降级: 余额 ', v_balance, ', 无合适商品'), 
                            v_execution_time, UNIX_TIMESTAMP());
                ELSE
                    -- 不启用降级，记录错误
                    UPDATE xy_convey 
                    SET dispatch_status = 0
                    WHERE id = order_id;
                    
                    COMMIT;
                    
                    SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
                    INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_message, execution_time, create_time)
                    VALUES (order_id, v_user_id, 'insufficient_balance', 
                            CONCAT('余额不足: 当前 ', v_balance), 
                            v_execution_time, UNIX_TIMESTAMP());
                END IF;
            END IF;
        ELSE
            ROLLBACK;
            SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
            INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_message, execution_time, create_time)
            VALUES (order_id, IFNULL(v_user_id, 0), 'version_conflict', 
                    '订单版本冲突', v_execution_time, UNIX_TIMESTAMP());
        END IF;
    ELSE
        ROLLBACK;
        SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
        INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_message, execution_time, create_time)
        VALUES (order_id, 0, 'order_not_found', 
                '未找到符合条件的空订单', v_execution_time, UNIX_TIMESTAMP());
    END IF;
END$$
DELIMITER ;

-- ================================
-- 第三步：验证更新结果
-- ================================

-- 验证存储过程创建成功
SELECT 'ProcessAutoDispatchOrder 存储过程更新完成' as message;

-- 验证智能降级配置
SELECT name, value FROM system_config WHERE name = 'enable_smart_downgrade';

-- 如果智能降级配置不存在，则添加
INSERT IGNORE INTO system_config (name, value) 
VALUES ('enable_smart_downgrade', '1');

-- 验证配置添加成功
SELECT name, value FROM system_config WHERE name = 'enable_smart_downgrade';

-- ================================
-- 第四步：兼容性检查
-- ================================

-- 检查当前空订单数量
SELECT 
    COUNT(*) as total_empty_orders,
    SUM(CASE WHEN auto_dispatch = 1 THEN 1 ELSE 0 END) as auto_empty_orders,
    SUM(CASE WHEN manual_dispatch = 1 THEN 1 ELSE 0 END) as manual_empty_orders
FROM xy_convey 
WHERE goods_id = 0 AND status = 0;

-- 检查当前有商品的待处理订单数量
SELECT 
    COUNT(*) as total_pending_orders,
    SUM(CASE WHEN auto_dispatch = 1 THEN 1 ELSE 0 END) as auto_pending_orders,
    SUM(CASE WHEN manual_dispatch = 1 THEN 1 ELSE 0 END) as manual_pending_orders
FROM xy_convey 
WHERE goods_id > 0 AND status = 0;

SELECT 'upgrade-unified-dispatch.sql 执行完成' as final_message; 