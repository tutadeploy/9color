-- 9Color前台用户数据清理脚本
-- 清理所有前台用户及其相关数据，保留管理员账户

-- 开始事务
START TRANSACTION;

-- 显示清理前的数据统计
SELECT '=== 清理前数据统计 ===' as info;
SELECT COUNT(*) as total_users FROM xy_users;
SELECT COUNT(*) as total_orders FROM xy_convey;
SELECT COUNT(*) as total_balance_logs FROM xy_balance_log;
SELECT COUNT(*) as total_messages FROM xy_message;
SELECT COUNT(*) as total_rewards FROM xy_reward_log;

-- 1. 删除前台用户的奖励记录
DELETE FROM xy_reward_log WHERE uid IN (SELECT id FROM xy_users);
SELECT ROW_COUNT() as deleted_reward_logs;

-- 2. 删除前台用户的余额日志
DELETE FROM xy_balance_log WHERE uid IN (SELECT id FROM xy_users);
SELECT ROW_COUNT() as deleted_balance_logs;

-- 3. 删除前台用户的消息
DELETE FROM xy_message WHERE uid IN (SELECT id FROM xy_users);
SELECT ROW_COUNT() as deleted_messages;

-- 4. 删除前台用户的订单
DELETE FROM xy_convey WHERE uid IN (SELECT id FROM xy_users);
SELECT ROW_COUNT() as deleted_orders;

-- 5. 删除前台用户的地址信息
DELETE FROM xy_member_address WHERE uid IN (SELECT id FROM xy_users);
SELECT ROW_COUNT() as deleted_addresses;

-- 6. 删除前台用户的错误记录
DELETE FROM xy_user_error WHERE uid IN (SELECT id FROM xy_users);
SELECT ROW_COUNT() as deleted_user_errors;

-- 7. 最后删除前台用户
DELETE FROM xy_users;
SELECT ROW_COUNT() as deleted_users;

-- 显示清理后的数据统计
SELECT '=== 清理后数据统计 ===' as info;
SELECT COUNT(*) as remaining_users FROM xy_users;
SELECT COUNT(*) as remaining_orders FROM xy_convey;
SELECT COUNT(*) as remaining_balance_logs FROM xy_balance_log;
SELECT COUNT(*) as remaining_messages FROM xy_message;
SELECT COUNT(*) as remaining_rewards FROM xy_reward_log;

-- 重置自增ID（可选）
-- ALTER TABLE xy_users AUTO_INCREMENT = 1;

SELECT '=== 清理完成 ===' as info;

-- 提交事务
COMMIT; 