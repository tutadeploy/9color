-- 修复自动派单存储过程重复扣款问题的补丁 v2
-- 问题：手动派单切换到自动派单时，存储过程没有检查是否已扣款，导致重复扣款
-- 解决：在扣款前检查余额日志，区分"已扣款"和"未扣款"两种情况

USE 6ui;

DELIMITER ;;

DROP PROCEDURE IF EXISTS `ProcessAutoDispatchOrder` ;;

CREATE DEFINER=`app`@`%` PROCEDURE `ProcessAutoDispatchOrder`(IN order_id VARCHAR(20))
BEGIN
    DECLARE v_user_id INT DEFAULT NULL;
    DECLARE v_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_commission DECIMAL(10,2) DEFAULT 0;
    DECLARE v_balance DECIMAL(10,2) DEFAULT 0;
    DECLARE v_version INT DEFAULT 0;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE v_start_time BIGINT DEFAULT 0;
    DECLARE v_execution_time INT DEFAULT 0;
    DECLARE v_payment_exists INT DEFAULT 0;  -- 新增：检查是否已扣款
    
    SET v_start_time = UNIX_TIMESTAMP() * 1000;
    
    START TRANSACTION;
    
    -- 获取订单信息
    SELECT uid, num, commission, version INTO v_user_id, v_amount, v_commission, v_version
    FROM xy_convey 
    WHERE id = order_id 
    AND auto_dispatch = 1 
    AND dispatch_status = 0 
    AND cooling_end_time > 0
    AND cooling_end_time <= UNIX_TIMESTAMP() 
    AND status = 0
    FOR UPDATE;
    
    IF v_user_id IS NOT NULL THEN
        -- 更新订单状态为处理中
        UPDATE xy_convey 
        SET dispatch_status = 999, version = version + 1 
        WHERE id = order_id AND version = v_version;
        
        SET v_affected_rows = ROW_COUNT();
        
        IF v_affected_rows > 0 THEN
            -- 获取用户余额
            SELECT balance INTO v_balance FROM xy_users WHERE id = v_user_id FOR UPDATE;
            
            -- 检查是否已存在扣款记录（手动派单时已扣款的情况）
            SELECT COUNT(*) INTO v_payment_exists 
            FROM xy_balance_log 
            WHERE oid = order_id AND type = 2 AND status = 2;
            
            IF v_payment_exists > 0 THEN
                -- 情况1：已扣款（手动派单切自动派单），只执行结算
                UPDATE xy_users 
                SET balance = balance + v_amount + v_commission,
                    freeze_balance = freeze_balance - v_amount - v_commission,
                    deal_status = 1
                WHERE id = v_user_id;
                
                UPDATE xy_convey 
                SET status = 1, dispatch_status = 1, c_status = 1, 
                    endtime = UNIX_TIMESTAMP(), version = version + 1
                WHERE id = order_id;
                
                -- 记录返佣日志：商品价格 + 佣金
                INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
                VALUES (v_user_id, order_id, v_amount + v_commission, 3, 1, UNIX_TIMESTAMP());
                
                INSERT INTO xy_reward_log (oid, uid, num, addtime, type) 
                VALUES (order_id, v_user_id, v_amount, UNIX_TIMESTAMP(), 2);
                
                COMMIT;
                
                SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
                
                INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, execution_time, create_time)
                VALUES (order_id, v_user_id, v_amount, v_commission, 'already_paid_auto', v_execution_time, CURRENT_TIMESTAMP);
                
            ELSE
                -- 情况2：未扣款（纯自动派单），执行完整的扣款+结算流程
                IF v_balance >= v_amount THEN
                    -- 先扣款（转为冻结余额）
                    UPDATE xy_users 
                    SET balance = balance - v_amount,
                        freeze_balance = freeze_balance + v_amount + v_commission
                    WHERE id = v_user_id;
                    
                    -- 再结算（返还本金+佣金）
                    UPDATE xy_users 
                    SET balance = balance + v_amount + v_commission,
                        freeze_balance = freeze_balance - v_amount - v_commission,
                        deal_status = 1
                    WHERE id = v_user_id;
                    
                    UPDATE xy_convey 
                    SET status = 1, dispatch_status = 1, c_status = 1, 
                        endtime = UNIX_TIMESTAMP(), version = version + 1
                    WHERE id = order_id;
                    
                    -- 记录扣款日志
                    INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
                    VALUES (v_user_id, order_id, v_amount, 2, 2, UNIX_TIMESTAMP());
                    
                    -- 记录返佣日志：商品价格 + 佣金
                    INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
                    VALUES (v_user_id, order_id, v_amount + v_commission, 3, 1, UNIX_TIMESTAMP());
                    
                    INSERT INTO xy_reward_log (oid, uid, num, addtime, type) 
                    VALUES (order_id, v_user_id, v_amount, UNIX_TIMESTAMP(), 2);
                    
                    COMMIT;
                    
                    SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
                    
                    INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, execution_time, create_time)
                    VALUES (order_id, v_user_id, v_amount, v_commission, 'auto_manual', v_execution_time, CURRENT_TIMESTAMP);
                    
                ELSE
                    -- 余额不足，恢复订单状态
                    UPDATE xy_convey 
                    SET dispatch_status = 0, version = version + 1 
                    WHERE id = order_id;
                    
                    UPDATE xy_users 
                    SET deal_status = 1
                    WHERE id = v_user_id;
                    
                    COMMIT;
                    
                    SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
                    
                    INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, error_msg, execution_time, create_time)
                    VALUES (order_id, v_user_id, v_amount, v_commission, 'insufficient_balance', 
                           CONCAT('需要:', v_amount, ', 余额:', v_balance), v_execution_time, CURRENT_TIMESTAMP);
                END IF;
            END IF;
        ELSE
            -- 订单已被其他进程处理
            ROLLBACK;
            INSERT INTO xy_auto_dispatch_log (order_id, status, error_msg, execution_time, create_time) 
            VALUES (order_id, 'skipped', '订单已被其他进程处理', 
                    ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), CURRENT_TIMESTAMP);
        END IF;
    ELSE
        -- 未找到符合条件的订单
        ROLLBACK;
        INSERT INTO xy_auto_dispatch_log (order_id, status, error_msg, execution_time, create_time) 
        VALUES (order_id, 'not_found', '未找到符合条件的订单', 
                ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), CURRENT_TIMESTAMP);
    END IF;
    
END ;;

DELIMITER ;

-- 创建备份表用于测试前的数据备份
CREATE TABLE IF NOT EXISTS `xy_convey_backup_before_fix` AS SELECT * FROM xy_convey WHERE 1=0;
CREATE TABLE IF NOT EXISTS `xy_balance_log_backup_before_fix` AS SELECT * FROM xy_balance_log WHERE 1=0;
CREATE TABLE IF NOT EXISTS `xy_users_backup_before_fix` AS SELECT * FROM xy_users WHERE 1=0;

-- 记录补丁应用时间
INSERT INTO xy_auto_dispatch_log (order_id, status, error_msg, execution_time, create_time) 
VALUES ('PATCH_APPLIED_V2', 'info', '应用重复扣款修复补丁v2', 0, CURRENT_TIMESTAMP); 