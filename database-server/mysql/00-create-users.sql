-- 9Color 数据库用户初始化
-- 在导入主数据库之前先创建用户

CREATE DATABASE IF NOT EXISTS `6ui` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 创建应用用户并授权
CREATE USER IF NOT EXISTS 'app'@'%' IDENTIFIED BY 'app123456';
GRANT ALL PRIVILEGES ON `6ui`.* TO 'app'@'%';

-- 创建只读用户
CREATE USER IF NOT EXISTS 'readonly'@'%' IDENTIFIED BY 'readonly123456';
GRANT SELECT ON `6ui`.* TO 'readonly'@'%';

-- 创建备份用户
CREATE USER IF NOT EXISTS 'backup'@'%' IDENTIFIED BY 'backup123456';
GRANT SELECT, LOCK TABLES, SHOW DATABASES, SHOW VIEW, EVENT, TRIGGER ON *.* TO 'backup'@'%';

-- 刷新权限
FLUSH PRIVILEGES;

SELECT '✅ 数据库和用户创建完成' as status; 