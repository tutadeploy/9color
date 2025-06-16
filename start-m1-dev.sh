#!/bin/bash

echo "启动9color PHP开发环境 (M芯片优化版本)..."

# 检查Docker是否运行
if ! docker info >/dev/null 2>&1; then
    echo "错误: Docker未运行，请先启动Docker Desktop"
    exit 1
fi

# 进入项目目录
cd "$(dirname "$0")"

# 停止可能存在的旧容器
echo "停止旧容器..."
docker compose -f dev-docker/docker-compose.m1.yml down

# 构建并启动容器
echo "构建和启动容器..."
docker compose -f dev-docker/docker-compose.m1.yml up --build -d

# 等待容器启动
echo "等待容器启动..."
sleep 10

# 检查容器状态
echo "检查容器状态..."
docker compose -f dev-docker/docker-compose.m1.yml ps

echo ""
echo "=================================="
echo "开发环境已启动！"
echo ""
echo "访问地址："
echo "• 主应用:  http://localhost:8080"
echo "• PHP服务器: http://localhost:8081"
echo "• Apache:  http://localhost:8082"
echo ""
echo "停止环境: docker compose -f dev-docker/docker-compose.m1.yml down"
echo "查看日志: docker compose -f dev-docker/docker-compose.m1.yml logs -f"
echo "=================================="
