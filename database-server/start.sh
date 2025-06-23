#!/bin/bash

# ===========================================
# 9Color 数据库服务器启动脚本
# 版本: v2.1.0
# 更新: 2025-06-23
# ===========================================

set -e

# 错误处理函数
cleanup_on_error() {
    print_error "启动过程中发生错误，正在清理..."
    $DOCKER_COMPOSE -f $COMPOSE_FILE down >/dev/null 2>&1 || true
    exit 1
}

# 设置错误陷阱
trap cleanup_on_error ERR

echo "=== 9Color数据库服务器启动 ==="

# 获取脚本所在目录
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

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

print_header() {
    echo "======================================="
    echo "        9Color 数据库服务器"
    echo "======================================="
}

# 环境检测函数
detect_environment() {
    print_info "检测运行环境..."

    if [[ "$OSTYPE" == "darwin"* ]]; then
        if [[ $(uname -m) == "arm64" ]]; then
            COMPOSE_FILE="docker-compose-m1.yml"
            print_info "检测到 M1/M2 Mac 环境"
        else
            COMPOSE_FILE="docker-compose.yml"
            print_info "检测到 Intel Mac 环境"
        fi
    else
        COMPOSE_FILE="docker-compose.yml"
        print_info "检测到 Linux 环境"
    fi

    # 设置Docker Compose命令
    if command -v "docker-compose" >/dev/null 2>&1; then
        DOCKER_COMPOSE="docker-compose"
    elif docker compose version >/dev/null 2>&1; then
        DOCKER_COMPOSE="docker compose"
    else
        print_error "未找到 docker-compose 或 docker compose 命令"
        exit 1
    fi
}

# 检查Docker是否运行
check_docker() {
    print_info "检查 Docker 环境..."

    if ! command -v docker &>/dev/null; then
        print_error "Docker 未安装，请先安装 Docker"
        exit 1
    fi

    if ! command -v docker-compose &>/dev/null; then
        print_error "Docker Compose 未安装，请先安装 Docker Compose"
        exit 1
    fi

    if ! docker info &>/dev/null; then
        print_error "Docker 服务未启动，请启动 Docker"
        exit 1
    fi

    print_success "Docker 环境检查通过"
}

# 预检查必要文件
pre_check() {
    print_info "检查必要文件..."

    # 检查Docker Compose文件
    if [ ! -f "$COMPOSE_FILE" ]; then
        print_error "Docker Compose配置文件不存在: $COMPOSE_FILE"
        exit 1
    fi

    # 检查MySQL初始化文件
    if [ ! -d "mysql" ]; then
        print_error "MySQL配置目录不存在: mysql/"
        exit 1
    fi

    # 检查脚本目录
    if [ ! -d "scripts" ]; then
        print_warning "脚本目录不存在，创建中..."
        mkdir -p scripts
    fi

    print_success "必要文件检查完成"
}

# 创建必要的目录并修复权限
create_directories() {
    print_info "创建必要的目录..."
    mkdir -p logs/mysql
    mkdir -p backup
    print_success "目录创建完成"
}

# 修复脚本权限
fix_permissions() {
    print_info "检查并修复脚本权限..."

    # 修复当前启动脚本权限
    chmod +x "$0" 2>/dev/null || true

    # 修复备份调度脚本权限
    if [ -f "scripts/backup-scheduler.sh" ]; then
        chmod +x scripts/backup-scheduler.sh
        print_success "备份调度脚本权限已修复"
    fi

    # 修复其他可能的脚本权限
    find scripts/ -name "*.sh" -type f -exec chmod +x {} \; 2>/dev/null || true

    print_success "脚本权限检查完成"
}

# 停止现有服务
stop_existing_services() {
    print_info "停止现有服务..."

    # 尝试停止可能存在的服务
    docker-compose -f docker-compose.yml down --remove-orphans 2>/dev/null || true
    docker-compose -f docker-compose-m1.yml down --remove-orphans 2>/dev/null || true

    print_success "现有服务已停止"
}

# 启动服务
start_services() {
    print_info "启动数据库服务..."
    print_info "使用配置文件: $COMPOSE_FILE"

    # 启动服务
    docker-compose -f "$COMPOSE_FILE" up -d

    if [ $? -eq 0 ]; then
        print_success "数据库服务启动成功"
    else
        print_error "数据库服务启动失败"
        exit 1
    fi
}

# 等待数据库就绪
wait_for_database() {
    print_info "等待数据库初始化完成..."

    local max_attempts=60
    local attempt=1

    while [ $attempt -le $max_attempts ]; do
        if docker exec 9color_mysql_standalone mysql -u app -papp123456 -e "SELECT 1;" &>/dev/null; then
            print_success "数据库连接成功"
            break
        fi

        if [ $attempt -eq $max_attempts ]; then
            print_error "数据库启动超时"
            print_info "请检查日志: docker logs 9color_mysql_standalone"
            exit 1
        fi

        echo -n "."
        sleep 2
        ((attempt++))
    done
    echo ""
}

# 检查自动派单系统
check_auto_dispatch() {
    print_info "检查自动派单系统状态..."

    # 检查事件调度器
    local event_scheduler=$(docker exec 9color_mysql_standalone mysql -u app -papp123456 -N -e "SHOW VARIABLES LIKE 'event_scheduler';" | awk '{print $2}')

    if [ "$event_scheduler" = "ON" ]; then
        print_success "事件调度器已启用"
    else
        print_warning "事件调度器未启用"
    fi

    # 检查自动派单事件
    local event_status=$(docker exec 9color_mysql_standalone mysql -u app -papp123456 -N -e "USE 6ui; SELECT Status FROM INFORMATION_SCHEMA.EVENTS WHERE EVENT_NAME = 'auto_dispatch_event';" 2>/dev/null || echo "NOT_FOUND")

    if [ "$event_status" = "ENABLED" ]; then
        print_success "自动派单事件已启用"
    elif [ "$event_status" = "DISABLED" ]; then
        print_warning "自动派单事件已禁用"
    else
        print_warning "自动派单事件未找到"
    fi

    # 检查存储过程
    local proc_count=$(docker exec 9color_mysql_standalone mysql -u app -papp123456 -N -e "USE 6ui; SELECT COUNT(*) FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA = '6ui' AND ROUTINE_NAME IN ('ProcessAutoDispatchOrder', 'ProcessAllExpiredOrders');" 2>/dev/null || echo "0")

    if [ "$proc_count" = "2" ]; then
        print_success "自动派单存储过程已就绪"
    else
        print_warning "自动派单存储过程不完整"
    fi

    # 检查修复补丁
    local patch_applied=$(docker exec 9color_mysql_standalone mysql -u app -papp123456 -N -e "USE 6ui; SELECT COUNT(*) FROM xy_auto_dispatch_log WHERE order_id = 'PATCH_APPLIED_V2';" 2>/dev/null || echo "0")

    if [ "$patch_applied" -gt "0" ]; then
        print_success "重复扣款修复补丁v2已应用"
    else
        print_warning "重复扣款修复补丁v2未应用"
    fi
}

# 显示服务状态
show_service_status() {
    print_info "服务状态："
    docker-compose -f "$COMPOSE_FILE" ps

    echo ""
    print_info "服务访问信息："
    echo "🗄️  MySQL 数据库:"
    echo "   - 主机: localhost"
    echo "   - 端口: 3306"
    echo "   - 数据库: 6ui"
    echo "   - 用户: app / app123456"
    echo "   - 管理员: root / root123456"
    echo ""
    echo "🌐 phpMyAdmin:"
    echo "   - 访问地址: http://localhost:8090"
    echo "   - 用户名: app 或 root"
    echo "   - 密码: 对应的数据库密码"
    echo ""
    echo "📊 监控命令:"
    echo "   - 查看日志: docker logs 9color_mysql_standalone"
    echo "   - 查看备份: docker logs 9color_mysql_backup"
    echo "   - 自动派单日志: docker exec 9color_mysql_standalone mysql -u app -papp123456 -e \"USE 6ui; SELECT * FROM xy_auto_dispatch_log ORDER BY create_time DESC LIMIT 10;\""
}

# 显示维护命令
show_maintenance_commands() {
    echo ""
    print_info "常用维护命令："
    echo "📋 服务管理:"
    echo "   docker-compose -f $COMPOSE_FILE ps              # 查看服务状态"
    echo "   docker-compose -f $COMPOSE_FILE logs mysql      # 查看MySQL日志"
    echo "   docker-compose -f $COMPOSE_FILE restart         # 重启所有服务"
    echo "   docker-compose -f $COMPOSE_FILE down            # 停止所有服务"
    echo ""
    echo "🎯 自动派单管理:"
    echo "   # 检查自动派单状态"
    echo "   docker exec 9color_mysql_standalone mysql -u app -papp123456 -e \"SHOW VARIABLES LIKE 'event_scheduler';\""
    echo "   # 查看自动派单事件"
    echo "   docker exec 9color_mysql_standalone mysql -u app -papp123456 -e \"USE 6ui; SHOW EVENTS;\""
    echo "   # 手动触发自动派单"
    echo "   docker exec 9color_mysql_standalone mysql -u app -papp123456 -e \"USE 6ui; CALL ProcessAllExpiredOrders();\""
    echo ""
    echo "💾 备份管理:"
    echo "   # 手动备份"
    echo "   docker exec 9color_mysql_standalone mysqldump -u root -proot123456 --single-transaction --routines --triggers --events 6ui > backup_\$(date +%Y%m%d).sql"
    echo "   # 恢复备份"
    echo "   docker exec -i 9color_mysql_standalone mysql -u root -proot123456 6ui < backup_20241201.sql"
}

# 主函数
main() {
    print_header

    # 检查环境
    detect_environment
    check_docker

    # 启动服务
    stop_existing_services
    start_services
    wait_for_database

    # 检查系统状态
    check_auto_dispatch

    # 显示状态信息
    show_service_status
    show_maintenance_commands

    echo ""
    print_success "9Color 数据库服务器启动完成！"
    print_info "配置版本: v2.1.0 (含自动派单修复补丁v2)"
    print_warning "生产环境操作前请务必备份数据！"
}

# 执行主函数
main "$@"
