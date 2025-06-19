#!/bin/bash

# 9Color 数据库服务器一键部署脚本
set -e

# 颜色输出
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# 配置变量
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_NAME="9color"
DB_CONTAINER_NAME="${PROJECT_NAME}_mysql_standalone"
BACKUP_CONTAINER_NAME="${PROJECT_NAME}_mysql_backup"

# 日志函数
log() {
    echo -e "$(date '+%Y-%m-%d %H:%M:%S') - $1"
}

error_exit() {
    log "${RED}错误: $1${NC}"
    exit 1
}

success() {
    log "${GREEN}成功: $1${NC}"
}

warning() {
    log "${YELLOW}警告: $1${NC}"
}

info() {
    log "${BLUE}信息: $1${NC}"
}

# 检查系统要求
check_requirements() {
    info "检查系统要求..."

    # 检查Docker
    if ! command -v docker &>/dev/null; then
        error_exit "Docker 未安装，请先安装 Docker"
    fi

    # 检查Docker Compose
    if ! docker compose version &>/dev/null; then
        error_exit "Docker Compose 未安装，请先安装 Docker Compose"
    fi

    # 检查磁盘空间（至少需要10GB）
    local available_space=$(df / | awk 'NR==2 {print $4}')
    local required_space=$((10 * 1024 * 1024)) # 10GB in KB

    if [[ $available_space -lt $required_space ]]; then
        warning "磁盘空间不足，建议至少保留10GB空间"
    fi

    success "系统要求检查通过"
}

# 创建目录结构
create_directories() {
    info "创建目录结构..."

    local dirs=(
        "mysql"
        "logs/mysql"
        "backup/daily"
        "backup/hourly"
        "backup/binlog"
        "backup/migration"
        "scripts"
        "ssl"
    )

    for dir in "${dirs[@]}"; do
        mkdir -p "$SCRIPT_DIR/$dir"
        info "创建目录: $dir"
    done

    success "目录结构创建完成"
}

# 设置文件权限
set_permissions() {
    info "设置文件权限..."

    # 设置脚本执行权限
    chmod +x "$SCRIPT_DIR/scripts/"*.sh 2>/dev/null || true

    # 设置日志目录权限
    chmod 755 "$SCRIPT_DIR/logs"
    chmod 755 "$SCRIPT_DIR/backup"

    success "文件权限设置完成"
}

# 创建环境变量文件
create_env_file() {
    local env_file="$SCRIPT_DIR/.env"

    if [[ ! -f "$env_file" ]]; then
        info "创建环境变量文件..."

        cat >"$env_file" <<EOF
# 9Color 数据库服务器环境变量配置

# MySQL 配置
MYSQL_ROOT_PASSWORD=root123456
MYSQL_DATABASE=6ui
MYSQL_USER=app
MYSQL_PASSWORD=app123456

# 备份配置
BACKUP_RETENTION_DAYS=5
BACKUP_SCHEDULE_HOUR=02

# 网络配置
DB_PORT=3306

# 时区配置
TZ=Asia/Shanghai

# 性能配置 - 针对4GB内存优化
MYSQL_INNODB_BUFFER_POOL_SIZE=768M
MYSQL_MAX_CONNECTIONS=300

# 安全配置
MYSQL_BIND_ADDRESS=0.0.0.0
EOF

        success "环境变量文件创建完成: $env_file"
        warning "请根据需要修改 $env_file 中的配置"
    else
        info "环境变量文件已存在: $env_file"
    fi
}

# 启动数据库服务
start_database() {
    info "启动数据库服务..."

    cd "$SCRIPT_DIR"

    # 停止现有服务
    docker compose down 2>/dev/null || true

    # 启动服务
    docker compose up -d

    # 等待MySQL启动
    info "等待MySQL服务启动..."
    local max_attempts=30
    local attempt=1

    while [[ $attempt -le $max_attempts ]]; do
        if docker exec "$DB_CONTAINER_NAME" mysql -uroot -p"${MYSQL_ROOT_PASSWORD:-root123456}" -e "SELECT 1;" &>/dev/null; then
            success "MySQL服务启动成功"
            break
        fi

        info "等待MySQL启动 (${attempt}/${max_attempts})..."
        sleep 10
        ((attempt++))
    done

    if [[ $attempt -gt $max_attempts ]]; then
        error_exit "MySQL服务启动超时"
    fi
}

# 显示服务状态
show_status() {
    info "服务状态:"
    docker compose ps

    info "数据库连接信息:"
    echo "  主机: $(hostname -I | awk '{print $1}')"
    echo "  端口: 3306"
    echo "  数据库: 6ui"
    echo "  用户: app"
    echo "  密码: app123456"
    echo ""

    info "管理命令:"
    echo "  查看日志: docker compose logs -f"
    echo "  进入MySQL: docker exec -it $DB_CONTAINER_NAME mysql -uroot -proot123456"
    echo "  停止服务: docker compose down"
    echo "  重启服务: docker compose restart"
}

# 创建监控脚本
create_monitoring_script() {
    local monitor_script="$SCRIPT_DIR/scripts/monitor.sh"

    cat >"$monitor_script" <<'EOF'
#!/bin/bash

# 数据库监控脚本
DB_CONTAINER="9color_mysql_standalone"
LOG_FILE="/backup/monitor.log"

log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
}

# 检查容器状态
if ! docker ps | grep -q "$DB_CONTAINER"; then
    log "错误: MySQL容器未运行"
    exit 1
fi

# 检查数据库连接
if ! docker exec "$DB_CONTAINER" mysql -uroot -proot123456 -e "SELECT 1;" &>/dev/null; then
    log "错误: 数据库连接失败"
    exit 1
fi

# 获取状态信息
CONNECTIONS=$(docker exec "$DB_CONTAINER" mysql -uroot -proot123456 -e "SHOW STATUS LIKE 'Threads_connected';" -N | awk '{print $2}')
QUERIES=$(docker exec "$DB_CONTAINER" mysql -uroot -proot123456 -e "SHOW STATUS LIKE 'Queries';" -N | awk '{print $2}')

log "监控检查 - 连接数: $CONNECTIONS, 查询数: $QUERIES"
EOF

    chmod +x "$monitor_script"
    success "监控脚本创建完成"
}

# 主函数
main() {
    echo "========================================"
    echo "9Color 数据库服务器部署"
    echo "========================================"

    check_requirements
    create_directories
    create_env_file
    set_permissions
    create_monitoring_script
    start_database
    show_status

    echo "========================================"
    success "数据库服务器部署完成!"
    echo "========================================"
    warning "请记住修改默认密码!"
    info "配置文件位置: $SCRIPT_DIR/.env"
    info "日志目录: $SCRIPT_DIR/logs"
    info "备份目录: $SCRIPT_DIR/backup"
}

# 信号处理
trap 'echo "部署中断"; exit 1' SIGINT SIGTERM

# 执行主程序
main "$@"
