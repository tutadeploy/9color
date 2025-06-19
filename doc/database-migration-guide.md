# 9Color 数据库分离部署完整指南

## 🎯 方案概述

本指南将帮助您将9Color项目的数据库从应用服务器分离到独立的数据库服务器，提高系统的稳定性、安全性和可扩展性。

## 📋 架构变化

### 现有架构
```
应用服务器 (Nginx + PHP-FPM + MySQL)
```

### 新架构
```
应用服务器 (Nginx + PHP-FPM) ←→ 数据库服务器 (MySQL + Redis + 备份服务)
```

## 🚀 部署流程

### 阶段一：准备数据库服务器

#### 1. 服务器要求
- **操作系统**: Ubuntu 20.04+ / CentOS 7+  
- **内存**: 最少 4GB，推荐 8GB+
- **磁盘**: 最少 50GB，推荐 SSD
- **网络**: 内网带宽 100Mbps+

#### 2. 部署数据库服务器

```bash
# 1. 创建部署目录
mkdir -p /opt/9color-database
cd /opt/9color-database

# 2. 下载配置文件（或手动创建）
# 复制 database-server/ 目录下的所有文件到此目录

# 3. 给脚本执行权限
chmod +x deploy-database.sh
chmod +x scripts/*.sh

# 4. 一键部署
./deploy-database.sh
```

#### 3. 验证数据库服务

```bash
# 检查服务状态
docker-compose ps

# 测试数据库连接
docker exec -it 9color_mysql_standalone mysql -uroot -proot123456

# 查看日志
docker-compose logs -f mysql
```

### 阶段二：数据迁移

#### 1. 准备迁移

```bash
# 在数据库服务器上
cd /opt/9color-database

# 给迁移脚本执行权限
chmod +x scripts/migrate-data.sh
```

#### 2. 执行数据迁移

```bash
# 基本迁移（本地到本地）
./scripts/migrate-data.sh

# 跨服务器迁移示例
./scripts/migrate-data.sh \
  192.168.1.100 3306 root oldpass 6ui \
  192.168.1.200 3306 root newpass 6ui
```

#### 3. 验证迁移结果

```bash
# 检查数据库
docker exec -it 9color_mysql_standalone mysql -uroot -proot123456 -e "USE 6ui; SHOW TABLES;"

# 检查数据量
docker exec -it 9color_mysql_standalone mysql -uroot -proot123456 -e "
SELECT 
  table_name,
  table_rows,
  ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size_MB'
FROM information_schema.tables 
WHERE table_schema = '6ui'
ORDER BY table_rows DESC;"
```

### 阶段三：更新应用服务器

#### 1. 更新应用配置

```bash
# 在应用服务器上
cd /path/to/nginx-php73-production

# 给更新脚本执行权限
chmod +x update-app-config.sh

# 执行配置更新
./update-app-config.sh
```

#### 2. 手动配置（可选）

如果不使用自动脚本，可以手动更新：

```bash
# 1. 备份原配置
cp ../config/database.php ../config/database.php.backup

# 2. 修改数据库配置
vim ../config/database.php
```

修改以下配置：
```php
return [
    'hostname'    => '192.168.1.200',  // 数据库服务器IP
    'database'    => '6ui',
    'username'    => 'app',
    'password'    => 'app123456',
    'hostport'    => '3306',
    // ... 其他配置保持不变
];
```

#### 3. 更新Docker配置

```bash
# 停止现有服务
docker-compose down

# 使用新的配置启动（移除MySQL服务）
docker-compose -f docker-compose-without-db.yml up -d

# 或者修改现有的 docker-compose.yml，移除 mysql 服务部分
```

### 阶段四：验证和监控

#### 1. 功能验证

```bash
# 1. 检查应用服务状态
docker-compose ps

# 2. 测试数据库连接
docker exec -it 9color_php73_prod php -r "
try {
    \$pdo = new PDO('mysql:host=192.168.1.200;port=3306;dbname=6ui', 'app', 'app123456');
    echo 'Database connection successful\n';
} catch (Exception \$e) {
    echo 'Connection failed: ' . \$e->getMessage() . '\n';
}"

# 3. 访问网站测试功能
curl -I http://localhost/
```

#### 2. 性能测试

```bash
# 数据库性能测试
docker exec -it 9color_mysql_standalone mysql -uroot -proot123456 -e "
SHOW GLOBAL STATUS LIKE 'Threads_connected';
SHOW GLOBAL STATUS LIKE 'Queries';
SHOW GLOBAL STATUS LIKE 'Slow_queries';"

# 网络延迟测试
ping -c 10 192.168.1.200
```

## 🔧 配置优化

### 数据库服务器优化

#### 1. MySQL 配置优化

```ini
# my.cnf 关键配置
[mysqld]
innodb_buffer_pool_size = 2G        # 设置为服务器内存的 60-70%
max_connections = 500               # 根据并发需求调整
innodb_log_file_size = 512M         # 提高写入性能
query_cache_size = 256M             # 查询缓存
```

#### 2. 系统级优化

```bash
# 调整系统参数
echo 'vm.swappiness = 10' >> /etc/sysctl.conf
echo 'net.core.somaxconn = 1024' >> /etc/sysctl.conf
sysctl -p

# 调整文件描述符限制
echo '* soft nofile 65535' >> /etc/security/limits.conf
echo '* hard nofile 65535' >> /etc/security/limits.conf
```

### 网络安全配置

#### 1. 防火墙设置

```bash
# 只允许应用服务器访问数据库
ufw allow from 192.168.1.100 to any port 3306
ufw allow from 192.168.1.100 to any port 6379

# 拒绝其他来源
ufw deny 3306
ufw deny 6379
```

#### 2. 数据库用户权限

```sql
-- 创建限制访问的用户
CREATE USER 'app'@'192.168.1.100' IDENTIFIED BY 'secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON 6ui.* TO 'app'@'192.168.1.100';

-- 删除通配符用户（如果存在）
DROP USER IF EXISTS 'app'@'%';
```

## 📊 备份策略

### 自动备份配置

备份调度器会自动执行以下任务：

1. **完整备份**: 每天凌晨2点
2. **增量备份**: 每小时（二进制日志）
3. **备份清理**: 自动删除过期备份

### 手动备份

```bash
# 完整备份
docker exec 9color_mysql_standalone mysqldump \
  -uroot -proot123456 \
  --single-transaction \
  --routines --triggers --events \
  6ui > backup_$(date +%Y%m%d).sql

# 压缩备份
gzip backup_$(date +%Y%m%d).sql
```

### 备份恢复

```bash
# 恢复数据库
gunzip -c backup_20240101.sql.gz | \
docker exec -i 9color_mysql_standalone mysql -uroot -proot123456 6ui
```

## 🚨 故障处理

### 常见问题

#### 1. 数据库连接失败

```bash
# 检查网络连通性
telnet 192.168.1.200 3306

# 检查防火墙
ufw status

# 检查MySQL服务
docker exec 9color_mysql_standalone mysqladmin ping -uroot -proot123456
```

#### 2. 性能问题

```bash
# 查看慢查询
docker exec 9color_mysql_standalone mysql -uroot -proot123456 -e "
SHOW VARIABLES LIKE 'slow_query_log';
SELECT * FROM mysql.slow_log LIMIT 10;"

# 查看连接状态
docker exec 9color_mysql_standalone mysql -uroot -proot123456 -e "
SHOW PROCESSLIST;"
```

#### 3. 磁盘空间不足

```bash
# 清理二进制日志
docker exec 9color_mysql_standalone mysql -uroot -proot123456 -e "
PURGE BINARY LOGS BEFORE DATE(NOW() - INTERVAL 3 DAY);"

# 清理备份文件
find /opt/9color-database/backup -name "*.gz" -mtime +7 -delete
```

### 应急回滚

如果新架构出现问题，可以快速回滚：

```bash
# 1. 停止应用服务
docker-compose down

# 2. 恢复原配置
cp ../config/database.php.backup ../config/database.php

# 3. 启动原有服务（包含数据库）
docker-compose -f docker-compose-original.yml up -d
```

## 📈 监控和维护

### 日常监控

```bash
# 数据库服务监控脚本
./scripts/monitor.sh

# 查看备份日志
tail -f backup/backup.log

# 检查磁盘使用率
df -h
```

### 性能监控

```bash
# MySQL 性能监控
docker exec 9color_mysql_standalone mysql -uroot -proot123456 -e "
SHOW GLOBAL STATUS LIKE 'Questions';
SHOW GLOBAL STATUS LIKE 'Uptime';
SHOW GLOBAL STATUS LIKE 'Slow_queries';"
```

### 定期维护

```bash
# 每周执行优化
docker exec 9color_mysql_standalone mysql -uroot -proot123456 -e "
OPTIMIZE TABLE 6ui.your_large_table;"

# 检查表完整性
docker exec 9color_mysql_standalone mysql -uroot -proot123456 -e "
CHECK TABLE 6ui.your_table;"
```

## 🔗 扩展方案

### 读写分离

如果需要进一步提升性能，可以配置读写分离：

1. 部署从数据库服务器
2. 配置主从复制
3. 修改应用配置支持读写分离

### 负载均衡

对于高并发场景，可以部署多个应用服务器：

```bash
# 使用 Nginx 作为负载均衡器
upstream app_servers {
    server 192.168.1.101:80;
    server 192.168.1.102:80;
    server 192.168.1.103:80;
}
```

## 📞 支持和联系

如果在部署过程中遇到问题，请检查：

1. 日志文件：`logs/` 目录下的各种日志
2. 容器状态：`docker-compose ps`
3. 网络连通性：`ping` 和 `telnet` 测试
4. 配置文件：检查各配置文件的语法和内容

---

**注意**: 请在生产环境部署前，先在测试环境完整测试整个流程！ 