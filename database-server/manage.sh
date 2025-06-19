#!/bin/bash

# 9Color 数据库服务器管理脚本
# 用法: ./manage.sh [start|stop|restart|status|logs|backup]

SCRIPT_DIR="$(dirname "$0")"
cd "$SCRIPT_DIR"

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 打印带颜色的消息
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# 显示帮助信息
show_help() {
    echo "9Color 数据库服务器管理脚本"
    echo ""
    echo "用法: $0 [命令]"
    echo ""
    echo "可用命令:"
    echo "  start     - 启动所有服务 (MySQL + 备份 + phpMyAdmin)"
    echo "  stop      - 停止所有服务"
    echo "  restart   - 重启所有服务"
    echo "  status    - 查看服务状态"
    echo "  logs      - 查看服务日志"
    echo "  backup    - 手动执行数据库备份"
    echo "  phpmyadmin- 单独重启phpMyAdmin"
    echo "  clean     - 清理停止的容器"
    echo "  help      - 显示此帮助信息"
    echo ""
    echo "示例:"
    echo "  $0 start     # 启动所有服务"
    echo "  $0 status    # 查看状态"
    echo "  $0 logs mysql # 查看MySQL日志"
}

# 启动服务
start_services() {
    print_info "启动9Color数据库服务器..."
    ./start.sh
}

# 停止服务
stop_services() {
    print_info "停止所有服务..."

    print_info "停止phpMyAdmin..."
    docker stop 9color_phpmyadmin 2>/dev/null || true

    print_info "停止备份服务..."
    docker stop 9color_mysql_backup 2>/dev/null || true

    print_info "停止MySQL服务..."
    docker stop 9color_mysql_standalone 2>/dev/null || true

    print_success "所有服务已停止"
}

# 重启服务
restart_services() {
    print_info "重启所有服务..."
    stop_services
    sleep 3
    start_services
}

# 查看服务状态
show_status() {
    echo "======================================="
    echo "        服务状态总览"
    echo "======================================="

    # 检查容器状态
    print_info "容器运行状态:"
    docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep -E "(NAMES|9color)"

    echo ""
    print_info "系统资源使用:"
    free -h | head -2

    echo ""
    print_info "磁盘使用:"
    df -h / | tail -1

    echo ""
    print_info "容器资源使用:"
    docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}" $(docker ps --format "{{.Names}}" | grep 9color | tr '\n' ' ')

    echo ""
    SERVER_IP=$(hostname -I | awk '{print $1}')
    print_info "访问地址:"
    echo "  📊 phpMyAdmin: http://$SERVER_IP:8090"
    echo "  🗄️  MySQL:     $SERVER_IP:3306"
}

# 查看日志
show_logs() {
    local service=$2
    case $service in
    mysql)
        print_info "MySQL日志 (最近20行):"
        docker logs 9color_mysql_standalone --tail 20
        ;;
    backup)
        print_info "备份服务日志 (最近20行):"
        docker logs 9color_mysql_backup --tail 20
        ;;
    phpmyadmin)
        print_info "phpMyAdmin日志 (最近20行):"
        docker logs 9color_phpmyadmin --tail 20
        ;;
    *)
        print_info "所有服务日志:"
        echo "=== MySQL ==="
        docker logs 9color_mysql_standalone --tail 10
        echo ""
        echo "=== 备份服务 ==="
        docker logs 9color_mysql_backup --tail 10
        echo ""
        echo "=== phpMyAdmin ==="
        docker logs 9color_phpmyadmin --tail 10
        ;;
    esac
}

# 手动备份
manual_backup() {
    print_info "执行手动数据库备份..."
    docker exec 9color_mysql_backup /scripts/backup-scheduler.sh manual
    print_success "备份完成"
}

# 重启phpMyAdmin
restart_phpmyadmin() {
    print_info "重启phpMyAdmin..."
    ./start-phpmyadmin.sh
}

# 清理停止的容器
clean_containers() {
    print_info "清理停止的容器..."
    docker container prune -f
    print_success "清理完成"
}

# 主逻辑
case "${1:-help}" in
start)
    start_services
    ;;
stop)
    stop_services
    ;;
restart)
    restart_services
    ;;
status)
    show_status
    ;;
logs)
    show_logs "$@"
    ;;
backup)
    manual_backup
    ;;
phpmyadmin)
    restart_phpmyadmin
    ;;
clean)
    clean_containers
    ;;
help | --help | -h)
    show_help
    ;;
*)
    print_error "未知命令: $1"
    echo ""
    show_help
    exit 1
    ;;
esac
