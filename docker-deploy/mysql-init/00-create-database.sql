-- 确保数据库存在并设置字符集
CREATE DATABASE IF NOT EXISTS `6ui` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 创建应用用户并授权
CREATE USER IF NOT EXISTS 'appuser'@'%' IDENTIFIED BY 'app123456';
GRANT ALL PRIVILEGES ON `6ui`.* TO 'appuser'@'%';

-- 刷新权限
FLUSH PRIVILEGES; 