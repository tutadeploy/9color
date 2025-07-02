-- 智能降级功能升级脚本
-- 修改自动派单存储过程，添加余额不足时的智能降级逻辑

USE 6ui;

-- 添加智能降级配置
INSERT IGNORE INTO system_config (name, value) 
VALUES ('enable_smart_downgrade', '1');

-- 修改存储过程：ProcessAutoDispatchOrder
DROP PROCEDURE IF EXISTS ProcessAutoDispatchOrder;

DELIMITER $$

CREATE PROCEDURE ProcessAutoDispatchOrder(IN order_id CHAR(18))
BEGIN
    DECLARE v_user_id INT DEFAULT NULL;
    DECLARE v_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_commission DECIMAL(10,2) DEFAULT 0;
    DECLARE v_balance DECIMAL(10,2) DEFAULT 0;
    DECLARE v_version INT DEFAULT 0;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE v_payment_exists INT DEFAULT 0;
    DECLARE v_start_time BIGINT DEFAULT 0;
    DECLARE v_execution_time BIGINT DEFAULT 0;
    DECLARE v_enable_smart_downgrade INT DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, error_message, execution_time, create_time)
        VALUES (order_id, IFNULL(v_user_id, 0), v_amount, v_commission, 'error', 
                CONCAT('SQL异常: ', @@error_count), 
                ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), UNIX_TIMESTAMP());
    END;
    
    SET v_start_time = UNIX_TIMESTAMP() * 1000;
    
    START TRANSACTION;
    
    SELECT uid, num, commission, version 
    INTO v_user_id, v_amount, v_commission, v_version
    FROM xy_convey 
    WHERE id = order_id AND auto_dispatch = 1 AND dispatch_status = 0 AND status = 0
    FOR UPDATE;
    
    IF v_user_id IS NOT NULL THEN
        
        -- 锁定订单，防止重复处理
        UPDATE xy_convey 
        SET dispatch_status = 999, version = version + 1 
        WHERE id = order_id AND version = v_version;
        
        SET v_affected_rows = ROW_COUNT();
        
        IF v_affected_rows > 0 THEN
            
            -- 获取用户当前余额
            SELECT balance INTO v_balance FROM xy_users WHERE id = v_user_id FOR UPDATE;
            
            -- 检查是否已经付款
            SELECT COUNT(*) INTO v_payment_exists 
            FROM xy_balance_log 
            WHERE oid = order_id AND type = 2 AND status = 2;
            
            IF v_payment_exists > 0 THEN
                -- 场景A：已付款订单，直接结算
                UPDATE xy_users 
                SET balance = balance + v_amount + v_commission,
                    freeze_balance = freeze_balance - v_amount - v_commission,
                    deal_status = 1
                WHERE id = v_user_id;
                
                UPDATE xy_convey 
                SET status = 1, dispatch_status = 1, c_status = 1, 
                    endtime = UNIX_TIMESTAMP(), version = version + 1
                WHERE id = order_id;
                
                -- 记录结算日志
                INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
                VALUES (v_user_id, order_id, v_amount + v_commission, 3, 1, UNIX_TIMESTAMP());
                
                INSERT INTO xy_reward_log (oid, uid, num, addtime, type) 
                VALUES (order_id, v_user_id, v_amount, UNIX_TIMESTAMP(), 2);
                
                COMMIT;
                
                SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
                
                INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, execution_time, create_time)
                VALUES (order_id, v_user_id, v_amount, v_commission, 'completed', v_execution_time, UNIX_TIMESTAMP());
                
            ELSE
                -- 场景B：未付款订单，需要扣款结算
                IF v_balance >= v_amount THEN
                    -- 余额充足，正常处理
                    UPDATE xy_users 
                    SET balance = balance - v_amount,
                        freeze_balance = freeze_balance + v_amount + v_commission
                    WHERE id = v_user_id;
                    
                    UPDATE xy_users 
                    SET balance = balance + v_amount + v_commission,
                        freeze_balance = freeze_balance - v_amount - v_commission,
                        deal_status = 1
                    WHERE id = v_user_id;
                    
                    UPDATE xy_convey 
                    SET status = 1, dispatch_status = 1, c_status = 1, 
                        endtime = UNIX_TIMESTAMP(), version = version + 1
                    WHERE id = order_id;
                    
                    INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
                    VALUES (v_user_id, order_id, v_amount, 2, 2, UNIX_TIMESTAMP());
                    
                    INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
                    VALUES (v_user_id, order_id, v_amount + v_commission, 3, 1, UNIX_TIMESTAMP());
                    
                    INSERT INTO xy_reward_log (oid, uid, num, addtime, type) 
                    VALUES (order_id, v_user_id, v_amount, UNIX_TIMESTAMP(), 2);
                    
                    COMMIT;
                    
                    SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
                    
                    INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, execution_time, create_time)
                    VALUES (order_id, v_user_id, v_amount, v_commission, 'completed', v_execution_time, UNIX_TIMESTAMP());
                    
                ELSE
                    -- 余额不足，检查是否启用智能降级
                    SELECT CAST(value AS UNSIGNED) INTO v_enable_smart_downgrade 
                    FROM system_config WHERE name = 'enable_smart_downgrade' LIMIT 1;
                    
                    IF v_enable_smart_downgrade = 1 THEN
                        -- 智能降级：转为手动派单
                        UPDATE xy_convey 
                        SET auto_dispatch = 0, 
                            manual_dispatch = 1, 
                            dispatch_status = 0,
                            cooling_end_time = 0,
                            goods_id = 0,
                            num = 0.00,
                            commission = 0.00,
                            version = version + 1
                        WHERE id = order_id;
                        
                        COMMIT;
                        
                        SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
                        
                        INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, error_message, execution_time, create_time)
                        VALUES (order_id, v_user_id, v_amount, v_commission, 'smart_downgrade', 
                                CONCAT('余额不足自动降级: 需要 ', v_amount, ', 当前 ', v_balance), 
                                v_execution_time, UNIX_TIMESTAMP());
                    ELSE
                        -- 不启用智能降级，记录余额不足错误
                        UPDATE xy_convey 
                        SET dispatch_status = 0, version = version + 1
                        WHERE id = order_id;
                        
                        COMMIT;
                        
                        SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
                        
                        INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, error_message, execution_time, create_time)
                        VALUES (order_id, v_user_id, v_amount, v_commission, 'insufficient_balance', 
                                CONCAT('余额不足: 需要 ', v_amount, ', 当前 ', v_balance), 
                                v_execution_time, UNIX_TIMESTAMP());
                    END IF;
                END IF;
            END IF;
        ELSE
            ROLLBACK;
            SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
            INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, error_message, execution_time, create_time)
            VALUES (order_id, IFNULL(v_user_id, 0), v_amount, v_commission, 'version_conflict', 
                    '订单版本冲突，可能已被其他进程处理', v_execution_time, UNIX_TIMESTAMP());
        END IF;
    ELSE
        ROLLBACK;
        SET v_execution_time = ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time);
        INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, error_message, execution_time, create_time)
        VALUES (order_id, 0, 0, 0, 'order_not_found', 
                '未找到符合条件的订单', v_execution_time, UNIX_TIMESTAMP());
    END IF;
END$$

DELIMITER ;

-- 验证脚本执行
SELECT 'upgrade-smart-downgrade.sql 执行完成' as message; 