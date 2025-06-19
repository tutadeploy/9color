#!/bin/bash

# 9Color 数据库迁移脚本
# 用于从现有数据库迁移数据到新的独立数据库服务器

set -e

# 颜色输出
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 配置变量
SOURCE_HOST=${1:-"localhost"}
SOURCE_PORT=${2:-"3306"}
SOURCE_USER=${3:-"root"}
SOURCE_PASS=${4:-"root123456"}
SOURCE_DB=${5:-"6ui"}

TARGET_HOST=${6:-"localhost"}
TARGET_PORT=${7:-"3306"}
TARGET_USER=${8:-"root"}
TARGET_PASS=${9:-"root123456"}
TARGET_DB=${10:-"6ui"}

BACKUP_DIR="/backup/migration"
LOG_FILE="$BACKUP_DIR/migration.log"

# 创建备份目录
mkdir -p "$BACKUP_DIR"

# 日志函数
log() {
    echo -e "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# 错误处理
error_exit() {
    log "${RED}错误: $1${NC}"
    exit 1
}

# 成功信息
success() {
    log "${GREEN}成功: $1${NC}"
}

# 警告信息
warning() {
    log "${YELLOW}警告: $1${NC}"
}

# 测试数据库连接
test_connection() {
    local host=$1
    local port=$2
    local user=$3
    local pass=$4
    local desc=$5

    log "测试 $desc 数据库连接..."
    if mysql -h "$host" -P "$port" -u "$user" -p"$pass" -e "SELECT 1;" >/dev/null 2>&1; then
        success "$desc 数据库连接正常"
        return 0
    else
        error_exit "$desc 数据库连接失败"
    fi
}

# 检查源数据库
check_source_database() {
    log "检查源数据库..."

    # 检查数据库是否存在
    local db_exists=$(mysql -h "$SOURCE_HOST" -P "$SOURCE_PORT" -u "$SOURCE_USER" -p"$SOURCE_PASS" \
        -e "SHOW DATABASES LIKE '$SOURCE_DB';" -N | wc -l)

    if [[ $db_exists -eq 0 ]]; then
        error_exit "源数据库 '$SOURCE_DB' 不存在"
    fi

    # 获取数据库大小
    local db_size=$(mysql -h "$SOURCE_HOST" -P "$SOURCE_PORT" -u "$SOURCE_USER" -p"$SOURCE_PASS" -e "
        SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size_MB'
        FROM information_schema.tables
        WHERE table_schema='$SOURCE_DB';" -N)

    # 获取表数量
    local table_count=$(mysql -h "$SOURCE_HOST" -P "$SOURCE_PORT" -u "$SOURCE_USER" -p"$SOURCE_PASS" \
        -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$SOURCE_DB';" -N)

    log "源数据库信息:"
    log "  - 数据库名: $SOURCE_DB"
    log "  - 数据库大小: ${db_size} MB"
    log "  - 表数量: $table_count"
}

# 导出数据
export_data() {
    local export_file="$BACKUP_DIR/6ui_migration_$(date +%Y%m%d_%H%M%S).sql"

    log "开始导出源数据库数据..."
    log "导出文件: $export_file"

    mysqldump \
        -h "$SOURCE_HOST" \
        -P "$SOURCE_PORT" \
        -u "$SOURCE_USER" \
        -p"$SOURCE_PASS" \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --opt \
        --add-drop-database \
        --databases "$SOURCE_DB" >"$export_file"

    if [[ $? -eq 0 ]]; then
        success "数据导出完成"

        # 压缩导出文件
        gzip "$export_file"
        success "导出文件已压缩: ${export_file}.gz"

        # 设置全局变量供后续使用
        EXPORT_FILE="${export_file}.gz"
    else
        error_exit "数据导出失败"
    fi
}

# 导入数据
import_data() {
    if [[ -z "$EXPORT_FILE" ]]; then
        error_exit "没有找到导出文件"
    fi

    log "开始导入数据到目标数据库..."
    log "导入文件: $EXPORT_FILE"

    # 解压缩文件
    local temp_file="${EXPORT_FILE%.gz}"
    gunzip -c "$EXPORT_FILE" >"$temp_file"

    # 导入数据
    mysql -h "$TARGET_HOST" -P "$TARGET_PORT" -u "$TARGET_USER" -p"$TARGET_PASS" <"$temp_file"

    if [[ $? -eq 0 ]]; then
        success "数据导入完成"

        # 删除临时文件
        rm -f "$temp_file"
    else
        error_exit "数据导入失败"
    fi
}

# 验证迁移结果
verify_migration() {
    log "验证迁移结果..."

    # 检查目标数据库表数量
    local target_table_count=$(mysql -h "$TARGET_HOST" -P "$TARGET_PORT" -u "$TARGET_USER" -p"$TARGET_PASS" \
        -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$TARGET_DB';" -N)

    # 检查源数据库表数量
    local source_table_count=$(mysql -h "$SOURCE_HOST" -P "$SOURCE_PORT" -u "$SOURCE_USER" -p"$SOURCE_PASS" \
        -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$SOURCE_DB';" -N)

    log "表数量对比:"
    log "  - 源数据库: $source_table_count 张表"
    log "  - 目标数据库: $target_table_count 张表"

    if [[ $source_table_count -eq $target_table_count ]]; then
        success "表数量验证通过"
    else
        warning "表数量不一致，请检查"
    fi

    # 检查目标数据库大小
    local target_db_size=$(mysql -h "$TARGET_HOST" -P "$TARGET_PORT" -u "$TARGET_USER" -p"$TARGET_PASS" -e "
        SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size_MB'
        FROM information_schema.tables
        WHERE table_schema='$TARGET_DB';" -N)

    log "目标数据库大小: ${target_db_size} MB"
}

# 主函数
main() {
    log "========================================"
    log "9Color 数据库迁移开始"
    log "========================================"
    log "源数据库: $SOURCE_HOST:$SOURCE_PORT/$SOURCE_DB"
    log "目标数据库: $TARGET_HOST:$TARGET_PORT/$TARGET_DB"
    log "========================================"

    # 测试连接
    test_connection "$SOURCE_HOST" "$SOURCE_PORT" "$SOURCE_USER" "$SOURCE_PASS" "源"
    test_connection "$TARGET_HOST" "$TARGET_PORT" "$TARGET_USER" "$TARGET_PASS" "目标"

    # 检查源数据库
    check_source_database

    # 确认是否继续
    echo -n "确认开始迁移？(y/N): "
    read -r confirm
    if [[ "$confirm" != "y" && "$confirm" != "Y" ]]; then
        log "迁移已取消"
        exit 0
    fi

    # 执行迁移
    export_data
    import_data
    verify_migration

    log "========================================"
    success "数据库迁移完成!"
    log "========================================"
    log "备份文件保存在: $EXPORT_FILE"
    log "日志文件: $LOG_FILE"
}

# 显示使用帮助
show_help() {
    echo "9Color 数据库迁移脚本"
    echo ""
    echo "使用方法:"
    echo "  $0 [源host] [源port] [源用户] [源密码] [源数据库] [目标host] [目标port] [目标用户] [目标密码] [目标数据库]"
    echo ""
    echo "默认参数:"
    echo "  源数据库: localhost:3306/6ui (root/root123456)"
    echo "  目标数据库: localhost:3306/6ui (root/root123456)"
    echo ""
    echo "示例:"
    echo "  $0"
    echo "  $0 192.168.1.100 3306 root oldpass 6ui 192.168.1.200 3306 root newpass 6ui"
}

# 检查参数
if [[ "$1" == "-h" || "$1" == "--help" ]]; then
    show_help
    exit 0
fi

# 执行主程序
main
