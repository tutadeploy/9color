-- ===========================================
-- 自动派单存储过程
-- Phase 5.2: 数据库级原子性处理方案
-- ===========================================

DELIMITER $$

-- ===========================================
-- 单订单原子性处理存储过程
-- ===========================================
DROP PROCEDURE IF EXISTS ProcessAutoDispatchOrder$$

CREATE PROCEDURE ProcessAutoDispatchOrder(IN order_id VARCHAR(20))
BEGIN
    DECLARE v_user_id INT DEFAULT NULL;
    DECLARE v_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_commission DECIMAL(10,2) DEFAULT 0;
    DECLARE v_balance DECIMAL(10,2) DEFAULT 0;
    DECLARE v_version INT DEFAULT 0;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE v_start_time BIGINT DEFAULT 0;
    DECLARE v_execution_time INT DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        GET DIAGNOSTICS CONDITION 1
        @sqlstate = RETURNED_SQLSTATE, @errno = MYSQL_ERRNO, @text = MESSAGE_TEXT;
        
        INSERT INTO xy_auto_dispatch_log (order_id, status, error_msg, execution_time, create_time) 
        VALUES (order_id, 'error', CONCAT('SQL异常 ', @errno, ': ', @text), 
                ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time), NOW());
    END;
    
    SET v_start_time = UNIX_TIMESTAMP(NOW(3)) * 1000;
    
    START TRANSACTION;
    
    -- 原子性检查并锁定订单（关键：使用版本号防止并发）
    SELECT uid, num, commission, version INTO v_user_id, v_amount, v_commission, v_version
    FROM xy_convey 
    WHERE id = order_id 
    AND auto_dispatch = 1 
    AND dispatch_status = 0 
    AND cooling_end_time > 0
    AND cooling_end_time <= UNIX_TIMESTAMP() 
    AND status = 0
    FOR UPDATE;
    
    -- 如果没有找到符合条件的订单，说明已被处理或状态已变更
    IF v_user_id IS NULL THEN
        ROLLBACK;
        LEAVE;
    END IF;
    
    -- 标记订单为处理中，同时更新版本号（防止其他进程处理）
    UPDATE xy_convey 
    SET dispatch_status = 999, version = version + 1 
    WHERE id = order_id AND version = v_version;
    
    SET v_affected_rows = ROW_COUNT();
    
    -- 如果更新失败，说明版本号已变更（被其他进程处理）
    IF v_affected_rows = 0 THEN
        ROLLBACK;
        INSERT INTO xy_auto_dispatch_log (order_id, status, error_msg, execution_time, create_time) 
        VALUES (order_id, 'skipped', '订单已被其他进程处理', 
                ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time), NOW());
        LEAVE;
    END IF;
    
    -- 检查用户余额
    SELECT balance INTO v_balance FROM xy_users WHERE id = v_user_id FOR UPDATE;
    
    IF v_balance >= v_amount THEN
        -- 执行付款：扣除余额，增加冻结余额
        UPDATE xy_users 
        SET balance = balance - v_amount,
            freeze_balance = freeze_balance + v_amount + v_commission
        WHERE id = v_user_id;
        
        -- 立即结算：返还本金+佣金，减少冻结余额
        UPDATE xy_users 
        SET balance = balance + v_amount + v_commission,
            freeze_balance = freeze_balance - v_amount - v_commission
        WHERE id = v_user_id;
        
        -- 更新订单为最终完成状态
        UPDATE xy_convey 
        SET status = 1, dispatch_status = 1, c_status = 1, 
            endtime = UNIX_TIMESTAMP(), version = version + 1
        WHERE id = order_id;
        
        -- 记录交易日志
        INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
        VALUES (v_user_id, order_id, v_amount, 2, 2, UNIX_TIMESTAMP());
        
        -- 记录佣金日志
        INSERT INTO xy_balance_log (uid, oid, num, type, status, addtime) 
        VALUES (v_user_id, order_id, v_commission, 3, 1, UNIX_TIMESTAMP());
        
        -- 记录奖励日志
        INSERT INTO xy_reward_log (oid, uid, num, addtime, type) 
        VALUES (order_id, v_user_id, v_amount, UNIX_TIMESTAMP(), 2);
        
        COMMIT;
        
        SET v_execution_time = ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time);
        
        -- 记录成功日志
        INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, execution_time, create_time) 
        VALUES (order_id, v_user_id, v_amount, v_commission, 'success', v_execution_time, NOW());
        
    ELSE
        -- 余额不足，恢复状态
        UPDATE xy_convey 
        SET dispatch_status = 0, version = version + 1 
        WHERE id = order_id;
        
        COMMIT;
        
        SET v_execution_time = ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time);
        
        -- 记录余额不足日志
        INSERT INTO xy_auto_dispatch_log (order_id, user_id, amount, commission, status, error_msg, execution_time, create_time) 
        VALUES (order_id, v_user_id, v_amount, v_commission, 'insufficient_balance', 
               CONCAT('需要:', v_amount, ', 余额:', v_balance), v_execution_time, NOW());
    END IF;
    
END$$

-- ===========================================
-- 批量处理存储过程
-- ===========================================
DROP PROCEDURE IF EXISTS ProcessAllExpiredOrders$$

CREATE PROCEDURE ProcessAllExpiredOrders()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_order_id VARCHAR(20);
    DECLARE v_found_count INT DEFAULT 0;
    DECLARE v_processed_count INT DEFAULT 0;
    DECLARE v_failed_count INT DEFAULT 0;
    DECLARE v_start_time BIGINT;
    DECLARE v_execution_time INT;
    
    DECLARE order_cursor CURSOR FOR 
        SELECT id FROM xy_convey 
        WHERE auto_dispatch = 1 
        AND dispatch_status = 0 
        AND cooling_end_time > 0 
        AND cooling_end_time <= UNIX_TIMESTAMP() 
        AND status = 0;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    SET v_start_time = UNIX_TIMESTAMP(NOW(3)) * 1000;
    
    -- 统计待处理订单数量
    SELECT COUNT(*) INTO v_found_count FROM xy_convey 
    WHERE auto_dispatch = 1 
    AND dispatch_status = 0 
    AND cooling_end_time > 0 
    AND cooling_end_time <= UNIX_TIMESTAMP() 
    AND status = 0;
    
    OPEN order_cursor;
    
    process_loop: LOOP
        FETCH order_cursor INTO v_order_id;
        IF done THEN
            LEAVE process_loop;
        END IF;
        
        -- 处理每个订单（独立事务）
        CALL ProcessAutoDispatchOrder(v_order_id);
        
        -- 检查处理结果
        IF (SELECT COUNT(*) FROM xy_auto_dispatch_log 
            WHERE order_id = v_order_id AND status = 'success' 
            AND create_time >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)) > 0 THEN
            SET v_processed_count = v_processed_count + 1;
        ELSE
            SET v_failed_count = v_failed_count + 1;
        END IF;
        
    END LOOP;
    
    CLOSE order_cursor;
    
    SET v_execution_time = ROUND((UNIX_TIMESTAMP(NOW(3)) * 1000) - v_start_time);
    
    -- 记录监控数据
    INSERT INTO xy_dispatch_monitor (found_orders, processed_orders, failed_orders, execution_time) 
    VALUES (v_found_count, v_processed_count, v_failed_count, v_execution_time);
    
END$$

DELIMITER ; 