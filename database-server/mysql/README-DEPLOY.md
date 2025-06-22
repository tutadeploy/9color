# 9Color 数据库部署说明

## 线上数据库初始化

### 文件说明

1. **00-create-users.sql** (732 bytes)
   - 创建数据库和用户
   - 设置权限
   - 优先执行

2. **01-complete-database.sql** (52KB)
   - 完整的数据库结构
   - 包含所有表、存储过程、触发器
   - 包含自动派单功能的所有组件
   - 包含基础配置数据

### 部署步骤

#### 方法1：Docker容器内执行
```bash
# 1. 进入MySQL容器
docker exec -it mysql_container_name mysql -u root -p

# 2. 执行初始化脚本
source /docker-entrypoint-initdb.d/00-create-users.sql;
source /docker-entrypoint-initdb.d/01-complete-database.sql;
```

#### 方法2：外部执行
```bash
# 1. 创建用户和数据库
mysql -h your_host -u root -p < 00-create-users.sql

# 2. 导入完整数据库
mysql -h your_host -u root -p 6ui < 01-complete-database.sql
```

### 验证部署

```sql
-- 检查数据库
USE 6ui;
SHOW TABLES;

-- 检查存储过程
SHOW PROCEDURE STATUS WHERE Db = '6ui';

-- 检查事件调度器
SHOW EVENTS;

-- 检查配置
SELECT * FROM system_config WHERE name LIKE '%dispatch%';
```

### 包含的功能

✅ **完整表结构** - 所有业务表  
✅ **自动派单系统** - 存储过程 + 事件调度器  
✅ **监控日志** - 处理日志和监控记录  
✅ **版本控制** - 乐观锁字段  
✅ **余额支持负数** - 手动派单强制付款  
✅ **基础配置** - 默认管理员和系统配置  

### 注意事项

- 确保MySQL版本 >= 5.7
- 确保启用事件调度器：`SET GLOBAL event_scheduler = ON;`
- 默认管理员：用户名 `admin`，密码 `123456`
- 数据库字符集：`utf8mb4_unicode_ci`

### 文件来源

这些SQL文件是从本地开发环境导出的完整数据库结构，包含了所有最新的功能和修改。 