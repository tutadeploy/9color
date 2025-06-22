-- 9Color 数据库用户初始化
-- 在导入主数据库之前先创建用户

CREATE DATABASE IF NOT EXISTS `6ui` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 使用数据库
USE `6ui`;

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

<<<<<<< Updated upstream:database-server/mysql/00-create-users.sql
SELECT '✅ 数据库和用户创建完成' as status; 
=======
-- 显示用户权限
SHOW GRANTS FOR 'app'@'%';
SHOW GRANTS FOR 'readonly'@'%';
SHOW GRANTS FOR 'backup'@'%'; 

-- 初始化完成提示
SELECT '=== 9Color数据库初始化完成 ===' as status; 
>>>>>>> Stashed changes:database-server/mysql/00-create-database.sql
