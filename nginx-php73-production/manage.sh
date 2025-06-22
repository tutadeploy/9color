#!/bin/bash

# 9Color一体化管理脚本
# 自动处理Docker服务启动 + 守护进程管理
# 跨平台兼容 (M1 macOS + Ubuntu22)

echo "=== 9Color一体化管理系统 ==="
echo "⚠️  注意: 请先启动独立数据库服务器 (../database-server/start.sh)"
echo "1. 一键启动 (Docker + 守护进程)"
echo "2. 停止守护进程"
echo "3. 查看状态"
echo "4. 测试功能"
echo "5. 查看日志"
echo "6. 重启守护进程"
echo "7. 完全停止 (停止所有服务)"
echo "8. 重新部署 (重启Docker + 守护进程)"
echo "================================="

# 自动检测运行环境
detect_environment() {
    # 检测操作系统
    if [[ "$OSTYPE" == "darwin"* ]]; then
        OS_TYPE="macos"
        echo "🍎 检测到 macOS 环境"
    elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
        OS_TYPE="linux"
        echo "🐧 检测到 Linux 环境"
    else
        OS_TYPE="unknown"
        echo "❓ 未知操作系统: $OSTYPE"
    fi

    # 检测CPU架构
    ARCH=$(uname -m)
    if [[ "$ARCH" == "arm64" ]] || [[ "$ARCH" == "aarch64" ]]; then
        echo "💪 检测到 ARM64 架构 (M1/M2)"
        IS_ARM=true
    else
        echo "🖥️  检测到 x86_64 架构"
        IS_ARM=false
    fi

    # 根据环境选择配置
    if [[ "$OS_TYPE" == "macos" ]] && [[ "$IS_ARM" == true ]]; then
        COMPOSE_FILE="docker-compose_m1.yml"
        WEB_PORT="9080"
        echo "📝 使用 M1 macOS 配置"
    else
        COMPOSE_FILE="docker-compose.yml"
        WEB_PORT="80"
        echo "📝 使用标准 Linux 配置"
    fi

    # 设置Docker Compose命令
    if command -v "docker-compose" >/dev/null 2>&1; then
        DOCKER_COMPOSE="docker-compose"
    elif docker compose version >/dev/null 2>&1; then
        DOCKER_COMPOSE="docker compose"
    else
        echo "❌ 未找到 docker-compose 或 docker compose 命令"
        exit 1
    fi
}

# 检查Docker是否运行
check_docker_service() {
    if ! docker version >/dev/null 2>&1; then
        echo "❌ Docker未运行，请先启动Docker"
        return 1
    fi
    return 0
}

# 启动Docker服务
start_docker_services() {
    echo "🚀 启动Docker服务..."

    # 确保在nginx-php73-production目录中
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" &>/dev/null && pwd)"
    cd "$SCRIPT_DIR"

    # 停止现有容器
    echo "🛑 停止现有容器..."
    $DOCKER_COMPOSE -f $COMPOSE_FILE down >/dev/null 2>&1

    # 构建并启动容器
    echo "🔨 构建并启动容器..."
    if $DOCKER_COMPOSE -f $COMPOSE_FILE up -d --build; then
        echo "✅ Docker服务启动成功"
    else
        echo "❌ Docker服务启动失败"
        return 1
    fi

    # 等待服务启动
    echo "⏳ 等待服务启动..."
    sleep 8

    # 检查容器状态
    if docker ps | grep -q "9color_php73_prod"; then
        echo "✅ 容器运行正常"
        echo "🌐 访问地址: http://localhost:$WEB_PORT"
    else
        echo "❌ 容器启动失败"
        return 1
    fi

    return 0
}

# 检查Docker服务是否运行
check_docker() {
    if ! check_docker_service; then
        return 1
    fi

    if ! docker ps | grep -q "9color_php73_prod"; then
        echo "❌ PHP容器未运行"
        return 1
    fi

    return 0
}

# 检查守护进程是否在运行
check_daemon_running() {
    # 检查进程名包含 auto_dispatch 或 9color-auto-dispatch
    local process_count=$(docker exec 9color_php73_prod ps aux 2>/dev/null | grep -E "(auto_dispatch|9color-auto-dispatch)" | grep -v grep | wc -l)
    if [ "$process_count" -gt 0 ]; then
        return 0 # 运行中
    else
        return 1 # 未运行
    fi
}

# 启动守护进程
start_daemon() {
    echo "🚀 启动守护进程..."

    # 先移除停止标记
    docker exec 9color_php73_prod php /var/www/html/public/index.php index/daemon/start >/dev/null 2>&1

    # 检查是否已有守护进程在运行
    if check_daemon_running; then
        echo "⚠️  守护进程已在运行中"
        return 0
    fi

    # 启动守护进程（后台运行）
    echo "正在启动守护进程..."
    docker exec -d 9color_php73_prod nohup php /var/www/html/public/index.php index/daemon/auto_dispatch >/dev/null 2>&1 &

    # 等待启动
    echo "等待守护进程启动..."
    sleep 5

    # 验证启动结果
    if check_daemon_running; then
        echo "✅ 守护进程启动成功"
        # 显示进程信息
        docker exec 9color_php73_prod ps aux | grep -E "(auto_dispatch|9color-auto-dispatch)" | grep -v grep
        return 0
    else
        echo "❌ 守护进程启动失败"
        echo "尝试查看启动日志..."
        docker logs --tail=10 9color_php73_prod 2>/dev/null | grep -E "(auto_dispatch|ERROR)" || echo "无相关日志"
        return 1
    fi
}

# 停止守护进程
stop_daemon() {
    echo "🛑 停止守护进程..."

    if ! check_daemon_running; then
        echo "ℹ️  守护进程未在运行"
        return 0
    fi

    docker exec 9color_php73_prod php /var/www/html/public/index.php index/daemon/stop >/dev/null 2>&1
    echo "✅ 已发送停止信号，等待进程停止..."

    # 等待进程停止
    for i in {1..10}; do
        sleep 2
        if ! check_daemon_running; then
            echo "✅ 守护进程已成功停止"
            return 0
        fi
        echo "   等待中... ($i/10)"
    done

    # 如果还在运行，强制停止
    echo "🔄 强制停止进程..."
    docker exec 9color_php73_prod pkill -f "auto_dispatch" 2>/dev/null || true
    sleep 2

    if ! check_daemon_running; then
        echo "✅ 守护进程已强制停止"
    else
        echo "❌ 无法停止守护进程"
        return 1
    fi
}

# 启动前检测环境
detect_environment

# 如果没有参数，显示菜单
if [ $# -eq 0 ]; then
    read -p "请选择操作 (1-8): " choice
else
    choice=$1
fi

case $choice in
1)
    echo "=== 一键启动 ==="

    # 检查Docker服务
    if ! check_docker_service; then
        echo "💡 请先启动Docker Desktop"
        exit 1
    fi

    # 启动Docker服务
    if ! check_docker; then
        echo "📦 Docker容器未运行，正在启动..."
        if ! start_docker_services; then
            echo "❌ Docker服务启动失败"
            exit 1
        fi
    else
        echo "✅ Docker服务已运行"
    fi

    # 启动守护进程
    if start_daemon; then
        echo ""
        echo "🎉 系统启动完成！"
        echo "🌐 前台地址: http://localhost:$WEB_PORT"
        echo "🔧 后台地址: http://localhost:$WEB_PORT/admin"
        echo ""
        echo "💡 管理命令:"
        echo "   查看状态: ./manage.sh 3"
        echo "   查看日志: ./manage.sh 5"
        echo "   停止系统: ./manage.sh 7"
    else
        echo "❌ 守护进程启动失败，请查看日志"
        echo "💡 执行: ./manage.sh 5"
    fi
    ;;
2)
    echo "=== 停止守护进程 ==="
    if check_docker; then
        stop_daemon
    else
        echo "❌ Docker服务未运行"
    fi
    ;;
3)
    echo "=== 查看系统状态 ==="

    echo "=== 环境信息 ==="
    echo "操作系统: $OS_TYPE"
    echo "CPU架构: $ARCH"
    echo "配置文件: $COMPOSE_FILE"
    echo "访问端口: $WEB_PORT"
    echo ""

    echo "=== Docker服务状态 ==="
    if check_docker_service; then
        if check_docker; then
            echo "✅ Docker服务正常运行"
            echo "✅ PHP容器运行正常"
            echo "🌐 访问地址: http://localhost:$WEB_PORT"
        else
            echo "⚠️  Docker运行中，但PHP容器未启动"
        fi
    else
        echo "❌ Docker服务未运行"
    fi

    echo ""
    echo "=== 守护进程状态 ==="
    if check_docker; then
        docker exec 9color_php73_prod php /var/www/html/public/index.php index/daemon/status 2>/dev/null || echo "❌ 无法获取守护进程状态"

        if check_daemon_running; then
            echo "✅ 守护进程正在运行"
            echo "进程详情:"
            docker exec 9color_php73_prod ps aux | grep -E "(auto_dispatch|9color-auto-dispatch)" | grep -v grep
        else
            echo "❌ 守护进程未运行"
            echo "💡 执行 './manage.sh 1' 启动守护进程"
        fi
    else
        echo "❌ 无法检查守护进程状态（Docker未运行）"
    fi
    ;;
4)
    echo "=== 测试守护进程功能 ==="
    if check_docker; then
        docker exec 9color_php73_prod php /var/www/html/public/index.php index/daemon/test
    else
        echo "❌ Docker服务未运行，无法测试"
    fi
    ;;
5)
    echo "=== 查看系统日志 ==="
    if check_docker; then
        echo "=== 最近的守护进程日志 ==="
        docker logs --tail=30 9color_php73_prod 2>/dev/null | grep -E "(auto_dispatch|SUCCESS|ERROR|HEARTBEAT|守护进程)" || echo "暂无相关日志"

        echo ""
        echo "=== 实时日志监控 ==="
        echo "按 Ctrl+C 退出日志监控"
        echo "开始监控..."
        docker logs -f 9color_php73_prod 2>/dev/null | grep --line-buffered -E "(auto_dispatch|SUCCESS|ERROR|HEARTBEAT|守护进程)"
    else
        echo "❌ Docker服务未运行，无法查看日志"
    fi
    ;;
6)
    echo "=== 重启守护进程 ==="
    if check_docker; then
        stop_daemon
        sleep 2
        start_daemon
    else
        echo "❌ Docker服务未运行"
    fi
    ;;
7)
    echo "=== 完全停止系统 ==="

    # 停止守护进程
    if check_docker; then
        stop_daemon
    fi

    # 停止Docker服务
    echo "🛑 停止Docker服务..."
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" &>/dev/null && pwd)"
    cd "$SCRIPT_DIR"
    $DOCKER_COMPOSE -f $COMPOSE_FILE down

    echo "✅ 系统已完全停止"
    ;;
8)
    echo "=== 重新部署系统 ==="

    # 停止所有服务
    echo "🛑 停止现有服务..."
    if check_docker; then
        stop_daemon
    fi

    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" &>/dev/null && pwd)"
    cd "$SCRIPT_DIR"
    $DOCKER_COMPOSE -f $COMPOSE_FILE down

    # 重新启动
    echo "🚀 重新部署..."
    if start_docker_services && start_daemon; then
        echo ""
        echo "🎉 系统重新部署完成！"
        echo "🌐 访问地址: http://localhost:$WEB_PORT"
    else
        echo "❌ 重新部署失败"
    fi
    ;;
*)
    echo "❌ 无效选择，请输入 1-8"
    exit 1
    ;;
esac
