#!/bin/bash

# 9Color数据库服务器智能启动脚本
# 自动识别环境并使用对应的配置文件

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

# 环境检测函数
detect_environment() {
    # 检测操作系统
    if [[ "$OSTYPE" == "darwin"* ]]; then
        OS_TYPE="macos"
        print_info "检测到 macOS 环境"
    elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
        OS_TYPE="linux"
        print_info "检测到 Linux 环境"
    else
        OS_TYPE="unknown"
        print_warning "未知操作系统: $OSTYPE"
    fi

    # 检测CPU架构
    ARCH=$(uname -m)
    if [[ "$ARCH" == "arm64" ]] || [[ "$ARCH" == "aarch64" ]]; then
        print_info "检测到 ARM64 架构 (M1/M2)"
        IS_ARM=true
    else
        print_info "检测到 x86_64 架构"
        IS_ARM=false
    fi

    # 根据环境选择配置文件
    if [[ "$OS_TYPE" == "macos" ]] && [[ "$IS_ARM" == true ]]; then
        COMPOSE_FILE="docker-compose-m1.yml"
        ENVIRONMENT="M1 macOS"
        print_info "使用 M1 macOS 配置文件: $COMPOSE_FILE"
    else
        COMPOSE_FILE="docker-compose.yml"
        ENVIRONMENT="Ubuntu22/x86_64"
        print_info "使用标准 Linux 配置文件: $COMPOSE_FILE"
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
    if ! docker version >/dev/null 2>&1; then
        print_error "Docker未运行，请先启动Docker"
        exit 1
    fi
    print_success "Docker服务正常运行"
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
    $DOCKER_COMPOSE -f $COMPOSE_FILE down >/dev/null 2>&1 || true
    print_success "现有服务已停止"
}

# 启动服务
start_services() {
    print_info "启动数据库服务..."

    if $DOCKER_COMPOSE -f $COMPOSE_FILE up -d --build; then
        print_success "数据库服务启动成功"
    else
        print_error "数据库服务启动失败"
        exit 1
    fi
}

# 等待服务就绪
wait_for_services() {
    print_info "等待服务就绪..."

    # 等待MySQL就绪
    local max_attempts=60
    local attempt=1

    while [ $attempt -le $max_attempts ]; do
        if docker exec 9color_mysql_standalone mysqladmin ping -h localhost -u root -proot123456 --silent >/dev/null 2>&1; then
            print_success "MySQL服务就绪"
            break
        fi

        if [ $attempt -eq $max_attempts ]; then
            print_error "MySQL启动超时"
            print_info "查看MySQL日志: docker logs 9color_mysql_standalone"
            exit 1
        fi

        echo -n "."
        sleep 2
        ((attempt++))
    done

    # 检查备份容器状态
    sleep 3
    if docker ps | grep -q "9color_mysql_backup.*Up"; then
        print_success "MySQL备份服务就绪"
    else
        print_warning "MySQL备份服务可能需要更多时间启动"
        print_info "查看备份服务日志: docker logs 9color_mysql_backup"
    fi

    # 等待phpMyAdmin就绪
    sleep 5
    if curl -s http://localhost:8090 >/dev/null 2>&1; then
        print_success "phpMyAdmin服务就绪"
    else
        print_warning "phpMyAdmin可能需要更多时间启动"
        print_info "查看phpMyAdmin日志: docker logs 9color_phpmyadmin"
    fi
}

# 显示服务信息
show_service_info() {
    echo ""
    echo "======================================="
    echo "        服务启动完成"
    echo "======================================="
    print_info "环境: $ENVIRONMENT"
    print_info "配置文件: $COMPOSE_FILE"
    echo ""
    print_success "服务访问地址:"
    echo "  📊 phpMyAdmin: http://localhost:8090"
    echo "  🗄️  MySQL:     localhost:3306"
    echo ""
    print_info "数据库连接信息:"
    echo "  数据库: 6ui"
    echo "  用户名: app"
    echo "  密码: app123456"
    echo "  Root密码: root123456"
    echo ""
    print_info "管理命令:"
    echo "  查看状态: docker ps"
    echo "  查看日志: docker logs 9color_mysql_standalone"
    echo "  停止服务: $DOCKER_COMPOSE -f $COMPOSE_FILE down"
    echo "======================================="
}

# 主执行流程
main() {
    detect_environment
    check_docker
    pre_check
    create_directories
    fix_permissions
    stop_existing_services
    start_services
    wait_for_services
    show_service_info
}

# 执行主函数
main "$@"
