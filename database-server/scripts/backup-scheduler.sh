#!/bin/bash

# 9Color 数据库自动备份调度器
# 每天执行完整备份，每小时执行增量备份

set -e

# 配置变量
DB_HOST=${MYSQL_HOST:-mysql}
DB_PORT=${MYSQL_PORT:-3306}
DB_USER=${MYSQL_USER:-backup}
DB_PASS=${MYSQL_ROOT_PASSWORD:-root123456}
DB_NAME=${MYSQL_DATABASE:-6ui}
BACKUP_DIR="/backup"
LOG_FILE="$BACKUP_DIR/backup.log"

# 创建备份目录
mkdir -p "$BACKUP_DIR/daily" "$BACKUP_DIR/hourly" "$BACKUP_DIR/binlog"

# 日志函数
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# 完整备份函数
full_backup() {
    local backup_file="$BACKUP_DIR/daily/6ui_full_$(date +%Y%m%d_%H%M%S).sql"
    log "开始完整备份: $backup_file"

    mysqldump \
        -h "$DB_HOST" \
        -P "$DB_PORT" \
        -u root \
        -p"$DB_PASS" \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --master-data=2 \
        --flush-logs \
        --opt \
        "$DB_NAME" >"$backup_file"

    # 压缩备份文件
    gzip "$backup_file"
    log "完整备份完成并压缩: ${backup_file}.gz"

    # 删除5天前的完整备份（适配40GB磁盘空间）
    find "$BACKUP_DIR/daily" -name "*.gz" -mtime +5 -delete
    log "清理5天前的完整备份"
}

# 增量备份函数（二进制日志）
incremental_backup() {
    local binlog_dir="$BACKUP_DIR/binlog"
    log "开始增量备份（二进制日志）"

    # 刷新二进制日志
    mysql -h "$DB_HOST" -P "$DB_PORT" -u root -p"$DB_PASS" -e "FLUSH LOGS;"

    # 复制二进制日志文件
    docker cp 9color_mysql_standalone:/var/lib/mysql/mysql-bin.* "$binlog_dir/" 2>/dev/null || true

    # 删除2天前的二进制日志备份（适配40GB磁盘空间）
    find "$binlog_dir" -name "mysql-bin.*" -mtime +2 -delete
    log "增量备份完成"
}

# 数据库监控检查
health_check() {
    log "执行数据库健康检查"

    # 检查数据库连接
    if ! mysql -h "$DB_HOST" -P "$DB_PORT" -u root -p"$DB_PASS" -e "SELECT 1;" >/dev/null 2>&1; then
        log "错误: 数据库连接失败"
        return 1
    fi

    # 检查数据库大小
    local db_size=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u root -p"$DB_PASS" -e "
        SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB'
        FROM information_schema.tables
        WHERE table_schema='$DB_NAME';" -N)

    log "数据库大小: ${db_size} MB"

    # 检查慢查询
    local slow_queries=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u root -p"$DB_PASS" -e "SHOW GLOBAL STATUS LIKE 'Slow_queries';" -N | awk '{print $2}')
    log "慢查询总数: $slow_queries"

    # 检查连接数
    local connections=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u root -p"$DB_PASS" -e "SHOW GLOBAL STATUS LIKE 'Threads_connected';" -N | awk '{print $2}')
    log "当前连接数: $connections"
}

# 主循环
main() {
    log "数据库备份调度器启动"

    while true; do
        current_hour=$(date +%H)
        current_minute=$(date +%M)

        # 每天凌晨2点执行完整备份
        if [[ "$current_hour" == "02" && "$current_minute" == "00" ]]; then
            full_backup
            health_check
            sleep 60 # 避免重复执行

        # 每小时的第0分钟执行增量备份（除了2点）
        elif [[ "$current_minute" == "00" && "$current_hour" != "02" ]]; then
            incremental_backup
            health_check
            sleep 60 # 避免重复执行
        fi

        # 每60秒检查一次
        sleep 60
    done
}

# 信号处理
trap 'log "备份调度器停止"; exit 0' SIGTERM SIGINT

# 启动主程序
main
