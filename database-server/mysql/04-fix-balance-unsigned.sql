-- 修复用户余额字段，允许负数（用于手动派单强制付款）
-- 
-- 问题：原来的balance字段定义为decimal(15,2) unsigned，不允许负数
-- 解决：移除unsigned约束，允许余额为负数
--
-- 执行时间：2025-06-22
-- 影响：允许手动派单时用户余额为负数

USE 6ui;

-- 修改balance字段，移除unsigned约束
ALTER TABLE xy_users MODIFY COLUMN balance decimal
(15,2) NOT NULL DEFAULT 0.00 COMMENT '用户余额，允许负数（手动派单强制付款）';

-- 验证修改结果
SELECT
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = '6ui'
    AND TABLE_NAME = 'xy_users'
    AND COLUMN_NAME = 'balance';

-- 记录修改日志
INSERT INTO xy_auto_dispatch_log
    (
    order_id,
    status,
    error_msg,
    execution_time,
    create_time
    )
VALUES
    (
        'SYSTEM_FIX_001',
        'success',
        '修复balance字段unsigned约束，允许负数余额',
        0,
        NOW()
);

SELECT '✅ balance字段已修复，现在支持负数余额' as result; 