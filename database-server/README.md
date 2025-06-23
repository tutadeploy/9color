# 9Color 数据库服务器配置

## 概述

本目录包含9Color电商平台的数据库服务器配置，基于MySQL 5.7，包含完整的数据库初始化、备份策略和自动派单系统。

**重要**: 本配置已与线上环境完全同步，包含最新的自动派单修复补丁v2。

## 🚀 快速启动

```bash
# 启动数据库服务
docker-compose up -d

# 查看服务状态
docker-compose ps

# 查看日志
docker-compose logs -f mysql
```

## 📁 目录结构

```
database-server/
├── docker-compose.yml          # Docker编排配置
├── docker-compose-m1.yml       # M1芯片Mac专用配置
├── start.sh                    # 启动脚本
├── README.md                   # 本文档
├── mysql/                      # MySQL配置
│   ├── 00-complete-init.sql    # 完整数据库初始化脚本（含v2修复）
│   ├── my.cnf                  # MySQL配置文件
│   └── fix-auto-dispatch-duplicate-payment-v2.sql  # 自动派单修复补丁
├── scripts/                    # 维护脚本
│   └── backup-scheduler.sh     # 数据库备份调度器
├── phpmyadmin/                 # phpMyAdmin配置
│   ├── config.user.inc.php     # phpMyAdmin用户配置
│   └── php-init.php           # PHP初始化配置
├── backup/                     # 备份存储目录
└── logs/                       # 日志存储目录
```

## 🔧 服务配置

### MySQL 主服务

- **镜像**: mysql:5.7
- **端口**: 3306
- **字符集**: utf8mb4
- **排序规则**: utf8mb4_unicode_ci
- **内存配置**: 768MB InnoDB缓冲池
- **事件调度器**: 启用（自动派单必需）

### 自动备份服务

- **完整备份**: 每日凌晨2点
- **增量备份**: 每小时执行
- **备份保留**: 完整备份5天，增量备份2天
- **监控**: 数据库健康检查

### phpMyAdmin 管理面板

- **访问地址**: http://localhost:8090
- **用户**: app / app123456
- **管理员**: root / root123456

## 🎯 自动派单系统

### 系统架构

本系统实现了智能的自动派单功能，支持A、B、C、D四种状态的智能切换：

- **A状态**: 自动派单+无商品，等待自动分配商品
- **B状态**: 自动派单+有商品，冷却计时或等待自动结算
- **C状态**: 手动派单+无商品，等待手动匹配商品  
- **D状态**: 手动派单+有商品，已派单等待手动结算

### 核心组件

#### 1. 存储过程

- **ProcessAutoDispatchOrder**: 处理单个订单的自动派单（v2修复版）
- **ProcessAllExpiredOrders**: 批量处理所有到期订单

#### 2. MySQL事件调度器

```sql
-- 自动派单事件（每分钟执行）
CREATE EVENT auto_dispatch_event
ON SCHEDULE EVERY 1 MINUTE
ON COMPLETION PRESERVE
ENABLE
DO CALL ProcessAllExpiredOrders();
```

#### 3. 重复扣款修复（v2）

**修复问题**: 手动派单切换到自动派单时的重复扣款问题

**解决方案**: 
- 在扣款前检查`xy_balance_log`表中是否已存在扣款记录
- 区分"已扣款"和"未扣款"两种情况
- `already_paid_auto`: 已扣款，只执行结算
- `auto_manual`: 未扣款，执行完整的扣款+结算流程

### 监控表

#### xy_auto_dispatch_log
记录所有自动派单的执行日志：

```sql
SELECT create_time, order_id, status, error_msg 
FROM xy_auto_dispatch_log 
ORDER BY create_time DESC LIMIT 10;
```

**状态说明**:
- `success`: 处理成功（旧版本）
- `auto_manual`: 完整的扣款+结算流程
- `already_paid_auto`: 已扣款，只执行结算
- `insufficient_balance`: 余额不足
- `skipped`: 订单已被其他进程处理
- `not_found`: 未找到符合条件的订单

## 🗄️ 数据库配置

### 用户权限

```sql
-- 应用用户（完全权限）
CREATE USER 'app'@'%' IDENTIFIED BY 'app123456';
GRANT ALL PRIVILEGES ON 6ui.* TO 'app'@'%';

-- 只读用户
CREATE USER 'readonly'@'%' IDENTIFIED BY 'readonly123456';
GRANT SELECT ON 6ui.* TO 'readonly'@'%';

-- 备份用户
CREATE USER 'backup'@'%' IDENTIFIED BY 'backup123456';
GRANT SELECT, LOCK TABLES, SHOW DATABASES, SHOW VIEW, EVENT, TRIGGER, PROCESS ON *.* TO 'backup'@'%';
```

### 关键配置

```ini
# my.cnf 关键配置
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
bind-address=0.0.0.0
log-bin=mysql-bin
binlog-format=ROW
expire-logs-days=7
max-connections=300
innodb-buffer-pool-size=768M
event_scheduler=ON  # 自动派单必需
```

## 🔄 备份策略

### 自动备份

备份调度器 (`backup-scheduler.sh`) 提供：

1. **完整备份** (每日2:00)
   - 包含结构、数据、存储过程、触发器、事件
   - 自动压缩存储
   - 保留5天

2. **增量备份** (每小时)
   - 二进制日志备份
   - 保留2天

3. **健康检查**
   - 数据库连接状态
   - 数据库大小监控
   - 慢查询统计
   - 连接数监控

### 手动备份

```bash
# 完整备份
docker exec 9color_mysql_standalone mysqldump \
  -u root -proot123456 \
  --single-transaction --routines --triggers --events \
  6ui > backup_$(date +%Y%m%d).sql

# 恢复备份
docker exec -i 9color_mysql_standalone mysql \
  -u root -proot123456 6ui < backup_20241201.sql
```

## 🛠️ 维护操作

### 检查自动派单状态

```sql
-- 检查事件调度器状态
SHOW VARIABLES LIKE 'event_scheduler';

-- 查看自动派单事件
SHOW EVENTS;

-- 查看最近的处理日志
SELECT * FROM xy_auto_dispatch_log ORDER BY create_time DESC LIMIT 10;

-- 查看待处理订单
SELECT COUNT(*) FROM xy_convey 
WHERE auto_dispatch = 1 AND dispatch_status = 0 
AND cooling_end_time > 0 AND cooling_end_time <= UNIX_TIMESTAMP() 
AND status = 0;
```

### 手动触发自动派单

```sql
-- 处理所有到期订单
CALL ProcessAllExpiredOrders();

-- 处理特定订单
CALL ProcessAutoDispatchOrder('UB2506232344012171');
```

### 启用/禁用自动派单

```sql
-- 启用自动派单事件
ALTER EVENT auto_dispatch_event ENABLE;

-- 禁用自动派单事件
ALTER EVENT auto_dispatch_event DISABLE;

-- 查看事件状态
SHOW EVENTS WHERE Name = 'auto_dispatch_event';
```

## 🚨 故障排除

### 常见问题

1. **自动派单不工作**
   - 检查事件调度器: `SHOW VARIABLES LIKE 'event_scheduler';`
   - 检查事件状态: `SHOW EVENTS;`
   - 查看错误日志: `SELECT * FROM xy_auto_dispatch_log WHERE status LIKE '%error%';`

2. **重复扣款问题**
   - 已通过v2补丁修复
   - 检查补丁应用状态: `SELECT * FROM xy_auto_dispatch_log WHERE order_id = 'PATCH_APPLIED_V2';`

3. **订单卡在冷却状态**
   - 检查冷却时间设置
   - 手动触发处理: `CALL ProcessAutoDispatchOrder('订单ID');`

### 日志查看

```bash
# MySQL错误日志
docker logs 9color_mysql_standalone

# 备份调度器日志
docker logs 9color_mysql_backup

# 自动派单日志（数据库内）
docker exec 9color_mysql_standalone mysql -u app -papp123456 \
  -e "USE 6ui; SELECT * FROM xy_auto_dispatch_log ORDER BY create_time DESC LIMIT 20;"
```

## 🔐 安全配置

### 网络安全

- 数据库仅监听内部网络
- 使用强密码策略
- 定期更新密码

### 备份安全

- 备份文件自动压缩
- 定期清理过期备份
- 备份目录权限控制

## 📊 性能优化

### 索引优化

关键表已创建必要索引：
- `xy_convey`: 订单查询索引
- `xy_balance_log`: 余额日志索引
- `xy_auto_dispatch_log`: 派单日志索引

### 内存配置

- InnoDB缓冲池: 768MB
- 最大连接数: 300
- 二进制日志保留: 7天

## 🌐 线上环境

### 生产服务器配置

**数据库服务器**: 38.180.150.127
- 用途: MySQL数据库 + 备份服务
- 配置: 与本地配置完全一致
- 监控: 自动备份 + 健康检查

**应用服务器**: 38.180.189.204
- 用途: Nginx + PHP-FPM
- 数据库连接: 指向数据库服务器

### 部署同步

本地配置与线上环境保持完全一致：
- ✅ Docker Compose配置同步
- ✅ MySQL配置文件同步
- ✅ 自动派单系统同步
- ✅ 备份策略同步
- ✅ 修复补丁同步

## 📝 更新日志

### v2.1.0 (2025-06-23)
- ✅ 修复自动派单重复扣款问题
- ✅ 添加已扣款订单的智能识别
- ✅ 优化存储过程性能
- ✅ 完善错误日志记录
- ✅ 与线上环境配置同步

### v2.0.0 (2025-06-22)
- ✅ 实现四状态智能切换系统
- ✅ 重构自动派单存储过程
- ✅ 添加MySQL事件调度器
- ✅ 完善监控和日志系统

### v1.0.0 (2025-06-20)
- ✅ 初始数据库架构
- ✅ 基础自动派单功能
- ✅ 数据库备份系统

## 📞 技术支持

如遇问题，请检查：
1. 容器运行状态: `docker-compose ps`
2. 服务日志: `docker-compose logs`
3. 数据库连接: 使用phpMyAdmin测试
4. 自动派单状态: 查询监控表

---

**注意**: 生产环境操作前请务必备份数据！
