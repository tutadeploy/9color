#!/bin/bash

# 9Color 应用服务器配置更新脚本
# 用于更新数据库连接配置指向独立数据库服务器

set -e

# 颜色输出
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# 配置变量
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_DIR="$SCRIPT_DIR/../config"
BACKUP_DIR="$SCRIPT_DIR/backup/config"

# 默认数据库服务器配置
DEFAULT_DB_HOST="192.168.1.200"
DEFAULT_DB_PORT="3306"
DEFAULT_DB_NAME="6ui"
DEFAULT_DB_USER="app"
DEFAULT_DB_PASS="app123456"

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

# 获取用户输入
get_db_config() {
    echo "========================================"
    echo "数据库服务器配置"
    echo "========================================"

    read -p "数据库服务器IP地址 [$DEFAULT_DB_HOST]: " DB_HOST
    DB_HOST=${DB_HOST:-$DEFAULT_DB_HOST}

    read -p "数据库端口 [$DEFAULT_DB_PORT]: " DB_PORT
    DB_PORT=${DB_PORT:-$DEFAULT_DB_PORT}

    read -p "数据库名 [$DEFAULT_DB_NAME]: " DB_NAME
    DB_NAME=${DB_NAME:-$DEFAULT_DB_NAME}

    read -p "数据库用户 [$DEFAULT_DB_USER]: " DB_USER
    DB_USER=${DB_USER:-$DEFAULT_DB_USER}

    read -s -p "数据库密码 [$DEFAULT_DB_PASS]: " DB_PASS
    DB_PASS=${DB_PASS:-$DEFAULT_DB_PASS}
    echo

    echo ""
    echo "配置信息："
    echo "  数据库服务器: $DB_HOST:$DB_PORT"
    echo "  数据库名: $DB_NAME"
    echo "  用户: $DB_USER"
    echo ""
}

# 测试数据库连接
test_database_connection() {
    info "测试数据库连接..."

    # 使用docker运行mysql客户端测试连接
    if docker run --rm mysql:5.7 mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" -e "SELECT 1;" &>/dev/null; then
        success "数据库连接测试成功"
    else
        error_exit "数据库连接测试失败，请检查配置"
    fi
}

# 备份现有配置
backup_config() {
    info "备份现有配置文件..."

    mkdir -p "$BACKUP_DIR"
    local timestamp=$(date +%Y%m%d_%H%M%S)

    if [[ -f "$CONFIG_DIR/database.php" ]]; then
        cp "$CONFIG_DIR/database.php" "$BACKUP_DIR/database_$timestamp.php"
        success "数据库配置已备份"
    fi

    if [[ -f "$SCRIPT_DIR/docker-compose.yml" ]]; then
        cp "$SCRIPT_DIR/docker-compose.yml" "$BACKUP_DIR/docker-compose_$timestamp.yml"
        success "Docker配置已备份"
    fi
}

# 更新数据库配置文件
update_database_config() {
    info "更新数据库配置文件..."

    local config_file="$CONFIG_DIR/database.php"

    cat >"$config_file" <<EOF
<?php

return [
    // 数据库调试模式
    'debug'       => false,
    // 数据库类型
    'type'        => 'mysql',
    // 服务器地址 - 指向独立数据库服务器
    'hostname'    => '$DB_HOST',
    // 数据库名
    'database'    => '$DB_NAME',
    // 用户名
    'username'    => '$DB_USER',
    // 密码
    'password'    => '$DB_PASS',
    // 编码
    'charset'     => 'utf8mb4',
    // 端口
    'hostport'    => '$DB_PORT',
    // 连接参数
    'params'      => [
        // 设置连接超时
        \PDO::ATTR_TIMEOUT => 30,
        // 持久连接
        \PDO::ATTR_PERSISTENT => false,
        // 设置字符集
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        // 错误模式
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ],
    // 主从
    'deploy'      => 0,
    // 分离
    'rw_separate' => false,
];
EOF

    success "数据库配置文件更新完成"
}

# 创建环境变量文件
create_env_file() {
    info "创建环境变量文件..."

    local env_file="$SCRIPT_DIR/.env"

    cat >"$env_file" <<EOF
# 9Color 应用服务器环境变量配置

# 数据库配置
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_NAME=$DB_NAME
DB_USER=$DB_USER
DB_PASS=$DB_PASS

# Redis配置
REDIS_HOST=$DB_HOST
REDIS_PORT=6379

# 时区配置
TZ=Asia/Shanghai

# PHP配置
PHP_MEMORY_LIMIT=256M
PHP_UPLOAD_MAX_FILESIZE=64M
PHP_POST_MAX_SIZE=64M

# Nginx配置
NGINX_WORKER_PROCESSES=auto
NGINX_WORKER_CONNECTIONS=1024
EOF

    success "环境变量文件创建完成: $env_file"
}

# 更新Docker Compose配置
update_docker_compose() {
    info "更新Docker Compose配置..."

    # 创建新的docker-compose配置（无数据库）
    local new_compose="$SCRIPT_DIR/docker-compose-new.yml"

    cat >"$new_compose" <<EOF
version: "3.8"

services:
  nginx:
    image: nginx:1.24-alpine
    container_name: 9color_nginx_prod
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ../:/var/www/html
      - ./nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./nginx/conf.d:/etc/nginx/conf.d
      - ./logs/nginx:/var/log/nginx
    depends_on:
      - php-fpm
    networks:
      - app-network
    restart: unless-stopped
    environment:
      - TZ=\${TZ:-Asia/Shanghai}

  php-fpm:
    build:
      context: ./php
      dockerfile: Dockerfile
    container_name: 9color_php73_prod
    volumes:
      - ../:/var/www/html
      - ./logs/php-fpm:/var/log/php-fpm
    networks:
      - app-network
    restart: unless-stopped
    environment:
      - PHP_FPM_USER=www-data
      - PHP_FPM_GROUP=www-data
      - TZ=\${TZ:-Asia/Shanghai}
      # 数据库连接配置
      - DB_HOST=\${DB_HOST:-$DB_HOST}
      - DB_PORT=\${DB_PORT:-$DB_PORT}
      - DB_NAME=\${DB_NAME:-$DB_NAME}
      - DB_USER=\${DB_USER:-$DB_USER}
      - DB_PASS=\${DB_PASS:-$DB_PASS}
      # Redis配置
      - REDIS_HOST=\${REDIS_HOST:-$DB_HOST}
      - REDIS_PORT=\${REDIS_PORT:-6379}

networks:
  app-network:
    driver: bridge
EOF

    success "新的Docker Compose配置创建完成"

    # 询问是否替换现有配置
    echo -n "是否替换现有的 docker-compose.yml？(y/N): "
    read -r replace_confirm
    if [[ "$replace_confirm" == "y" || "$replace_confirm" == "Y" ]]; then
        mv "$new_compose" "$SCRIPT_DIR/docker-compose.yml"
        success "Docker Compose配置已更新"
    else
        info "新配置保存为: docker-compose-new.yml"
    fi
}

# 重启应用服务
restart_application() {
    info "重启应用服务..."

    cd "$SCRIPT_DIR"

    # 停止现有服务
    docker-compose down

    # 启动服务
    docker-compose up -d

    # 等待服务启动
    sleep 10

    # 检查服务状态
    if docker-compose ps | grep -q "Up"; then
        success "应用服务重启成功"
    else
        error_exit "应用服务启动失败"
    fi
}

# 验证应用连接
verify_application() {
    info "验证应用数据库连接..."

    # 检查PHP容器中的数据库连接
    local php_container="9color_php73_prod"

    if docker exec "$php_container" php -r "
        try {
            \$pdo = new PDO('mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4', '$DB_USER', '$DB_PASS');
            echo 'Database connection successful';
        } catch (Exception \$e) {
            echo 'Database connection failed: ' . \$e->getMessage();
            exit(1);
        }
    " 2>/dev/null; then
        success "应用数据库连接验证成功"
    else
        warning "应用数据库连接验证失败，请检查配置"
    fi
}

# 显示最终状态
show_final_status() {
    echo "========================================"
    success "应用服务器配置更新完成!"
    echo "========================================"

    info "配置信息:"
    echo "  数据库服务器: $DB_HOST:$DB_PORT"
    echo "  数据库名: $DB_NAME"
    echo "  配置备份目录: $BACKUP_DIR"
    echo ""

    info "服务状态:"
    docker-compose ps
    echo ""

    info "常用管理命令:"
    echo "  查看日志: docker-compose logs -f"
    echo "  重启应用: docker-compose restart"
    echo "  停止应用: docker-compose down"
    echo "  启动应用: docker-compose up -d"
}

# 主函数
main() {
    echo "========================================"
    echo "9Color 应用服务器配置更新"
    echo "========================================"

    get_db_config

    # 确认是否继续
    echo -n "确认更新配置？(y/N): "
    read -r confirm
    if [[ "$confirm" != "y" && "$confirm" != "Y" ]]; then
        log "配置更新已取消"
        exit 0
    fi

    test_database_connection
    backup_config
    update_database_config
    create_env_file
    update_docker_compose
    restart_application
    verify_application
    show_final_status
}

# 显示帮助
show_help() {
    echo "9Color 应用服务器配置更新脚本"
    echo ""
    echo "用法: $0 [选项]"
    echo ""
    echo "选项:"
    echo "  -h, --help    显示此帮助信息"
    echo ""
    echo "此脚本将："
    echo "  1. 备份现有配置"
    echo "  2. 更新数据库连接配置"
    echo "  3. 创建环境变量文件"
    echo "  4. 更新Docker Compose配置"
    echo "  5. 重启应用服务"
    echo "  6. 验证数据库连接"
}

# 检查参数
if [[ "$1" == "-h" || "$1" == "--help" ]]; then
    show_help
    exit 0
fi

# 执行主程序
main "$@"
