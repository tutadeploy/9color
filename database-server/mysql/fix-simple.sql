-- 简化版修复：支持处理已有商品的订单
USE 6ui;

DROP PROCEDURE IF EXISTS ProcessAutoDispatchOrder;

CREATE PROCEDURE ProcessAutoDispatchOrder(IN order_id CHAR(18))
BEGIN
    DECLARE v_user_id INT DEFAULT NULL;
    DECLARE v_balance DECIMAL(10,2) DEFAULT 0;
    DECLARE v_user_level INT DEFAULT 0;
    DECLARE v_order_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_commission DECIMAL(10,2) DEFAULT 0;
    DECLARE v_goods_id INT DEFAULT 0;
    DECLARE v_goods_price DECIMAL(10,2) DEFAULT 0;
    DECLARE v_version INT DEFAULT 0;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE v_enable_smart_downgrade INT DEFAULT 0;
    DECLARE v_start_time BIGINT DEFAULT 0;
    DECLARE v_execution_time BIGINT DEFAULT 0;
    DECLARE v_payment_exists INT DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_msg, execution_time, create_time)
        VALUES (order_id, IFNULL(v_user_id, 0), 'error', 'SQL异常', ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), UNIX_TIMESTAMP());
    END;
    
    SET v_start_time = UNIX_TIMESTAMP() * 1000;
    START TRANSACTION;
    
    -- 查找符合条件的自动派单订单（移除goods_id=0限制）
    SELECT uid, version, goods_id, num, commission
    INTO v_user_id, v_version, v_goods_id, v_order_amount, v_commission
    FROM xy_convey 
    WHERE id = order_id 
      AND auto_dispatch = 1 
      AND dispatch_status = 0 
      AND status = 0
    FOR UPDATE;
    
    IF v_user_id IS NOT NULL THEN
        -- 锁定订单
        UPDATE xy_convey 
        SET dispatch_status = 999, version = version + 1 
        WHERE id = order_id AND version = v_version;
        
        SET v_affected_rows = ROW_COUNT();
        
        IF v_affected_rows > 0 THEN
            -- 获取用户信息
            SELECT balance, level INTO v_balance, v_user_level 
            FROM xy_users WHERE id = v_user_id FOR UPDATE;
            
            IF v_goods_id = 0 THEN
                -- 空订单：智能匹配商品
                SELECT id, goods_price 
                INTO v_goods_id, v_goods_price
                FROM xy_goods_list 
                WHERE status = 1 AND goods_price <= v_balance AND goods_price >= (v_balance * 0.1)
                ORDER BY RAND() LIMIT 1;
                
                IF v_goods_id > 0 AND v_balance >= v_goods_price THEN
                    -- 匹配成功，完整流程
                    SELECT COALESCE(v_goods_price * bili, v_goods_price * 0.025) INTO v_commission
                    FROM xy_level WHERE level = v_user_level LIMIT 1;
                    
                    SET v_order_amount = v_goods_price;
                    
                    UPDATE xy_convey 
                    SET goods_id = v_goods_id, goods_count = 1, num = v_order_amount, commission = v_commission, dispatch_status = 0
                    WHERE id = order_id;
                    
                    UPDATE xy_users SET balance = balance - v_order_amount, deal_status = 3 WHERE id = v_user_id;
                    UPDATE xy_users SET balance = balance + v_order_amount + v_commission, deal_status = 1 WHERE id = v_user_id;
                    
                    UPDATE xy_convey SET status = 1, dispatch_status = 1, c_status = 1, endtime = UNIX_TIMESTAMP() WHERE id = order_id;
                    
                    INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) VALUES (v_user_id, order_id, v_order_amount, 2, 2, UNIX_TIMESTAMP());
                    INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) VALUES (v_user_id, order_id, v_order_amount + v_commission, 3, 1, UNIX_TIMESTAMP());
                    INSERT INTO xy_reward_log (oid, uid, num, addtime, type) VALUES (order_id, v_user_id, v_order_amount, UNIX_TIMESTAMP(), 2);
                    
                    COMMIT;
                    INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, execution_time, create_time)
                    VALUES (order_id, v_user_id, v_order_amount, v_commission, 'completed_empty', ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), UNIX_TIMESTAMP());
                ELSE
                    -- 智能降级
                    SELECT CAST(value AS UNSIGNED) INTO v_enable_smart_downgrade FROM system_config WHERE name = 'enable_smart_downgrade' LIMIT 1;
                    IF v_enable_smart_downgrade = 1 THEN
                        UPDATE xy_convey SET auto_dispatch = 0, manual_dispatch = 1, dispatch_status = 0, cooling_end_time = 0 WHERE id = order_id;
                        COMMIT;
                        INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_msg, execution_time, create_time)
                        VALUES (order_id, v_user_id, 'smart_downgrade', CONCAT('余额不足: ', v_balance), ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), UNIX_TIMESTAMP());
                    ELSE
                        UPDATE xy_convey SET dispatch_status = 0 WHERE id = order_id;
                        COMMIT;
                        INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_msg, execution_time, create_time)
                        VALUES (order_id, v_user_id, 'insufficient_balance', CONCAT('余额不足: ', v_balance), ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), UNIX_TIMESTAMP());
                    END IF;
                END IF;
            ELSE
                -- 已有商品订单：检查扣款状态并结算
                SELECT COUNT(*) INTO v_payment_exists FROM xy_balance_log WHERE oid = order_id AND type = 2 AND status = 2;
                
                IF v_payment_exists > 0 THEN
                    -- 已扣款，直接结算
                    UPDATE xy_users SET balance = balance + v_order_amount + v_commission, deal_status = 1 WHERE id = v_user_id;
                    UPDATE xy_convey SET status = 1, dispatch_status = 1, c_status = 1, endtime = UNIX_TIMESTAMP() WHERE id = order_id;
                    
                    INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) VALUES (v_user_id, order_id, v_order_amount + v_commission, 3, 1, UNIX_TIMESTAMP());
                    INSERT INTO xy_reward_log (oid, uid, num, addtime, type) VALUES (order_id, v_user_id, v_order_amount, UNIX_TIMESTAMP(), 2);
                    
                    COMMIT;
                    INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, execution_time, create_time)
                    VALUES (order_id, v_user_id, v_order_amount, v_commission, 'completed_existing', ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), UNIX_TIMESTAMP());
                ELSE
                    -- 未扣款异常
                    UPDATE xy_convey SET dispatch_status = 0 WHERE id = order_id;
                    COMMIT;
                    INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_msg, execution_time, create_time)
                    VALUES (order_id, v_user_id, 'unpaid_existing_order', '有商品但未扣款', ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), UNIX_TIMESTAMP());
                END IF;
            END IF;
        ELSE
            ROLLBACK;
            INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_msg, execution_time, create_time)
            VALUES (order_id, IFNULL(v_user_id, 0), 'version_conflict', '版本冲突', ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), UNIX_TIMESTAMP());
        END IF;
    ELSE
        ROLLBACK;
        INSERT INTO xy_auto_dispatch_log (order_id, user_id, status, error_msg, execution_time, create_time)
        VALUES (order_id, 0, 'order_not_found', '未找到符合条件的订单', ROUND((UNIX_TIMESTAMP() * 1000) - v_start_time), UNIX_TIMESTAMP());
    END IF;
END;

SELECT 'ProcessAutoDispatchOrder 修复完成 - 支持已有商品订单' as message; 