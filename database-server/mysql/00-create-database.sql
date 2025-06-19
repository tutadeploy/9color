-- 9Color 数据库初始化脚本
-- 确保数据库存在并设置字符集
CREATE DATABASE IF NOT EXISTS `6ui` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 创建应用用户并授权
CREATE USER IF NOT EXISTS 'app'@'%' IDENTIFIED BY 'app123456';
GRANT ALL PRIVILEGES ON `6ui`.* TO 'app'@'%';

-- 创建只读用户（用于报表查询，减少主库压力）
CREATE USER IF NOT EXISTS 'readonly'@'%' IDENTIFIED BY 'readonly123456';
GRANT SELECT ON `6ui`.* TO 'readonly'@'%';

-- 创建备份用户
CREATE USER IF NOT EXISTS 'backup'@'%' IDENTIFIED BY 'backup123456';
GRANT SELECT, LOCK TABLES, SHOW DATABASES, SHOW VIEW, EVENT, TRIGGER ON *.* TO 'backup'@'%';

-- 刷新权限
FLUSH PRIVILEGES;

-- 显示用户权限
SHOW GRANTS FOR 'app'@'%';
SHOW GRANTS FOR 'readonly'@'%';
SHOW GRANTS FOR 'backup'@'%'; 