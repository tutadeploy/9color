#!/bin/bash

# 9Color 生产环境一键部署脚本
# 包含 PHP + Nginx + MySQL

set -e

echo "=== 9Color 生产环境一键部署脚本 ==="

# 检查系统
if ! grep -q "Ubuntu" /etc/os-release; then
    echo "警告: 此脚本主要为Ubuntu系统设计"
fi

# 检查Docker是否安装
if ! command -v docker &> /dev/null; then
    echo "正在安装 Docker..."
    sudo apt update
    sudo apt install -y apt-transport-https ca-certificates curl gnupg lsb-release
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
    echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
    sudo apt update
    sudo apt install -y docker-ce docker-ce-cli containerd.io
    
    # 添加当前用户到docker组
    sudo usermod -aG docker $USER
    echo "Docker 安装完成，请重新登录或运行 'newgrp docker' 以应用组权限"
fi

# 检查Docker Compose是否安装
if ! command -v docker-compose &> /dev/null; then
    echo "正在安装 Docker Compose..."
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
fi

# 检查端口是否被占用
echo "检查端口占用..."
if netstat -tuln 2>/dev/null | grep -q :80; then
    echo "错误: 端口 80 已被占用，请停止其他Web服务"
    exit 1
fi

if netstat -tuln 2>/dev/null | grep -q :3306; then
    echo "错误: 端口 3306 已被占用，请停止其他MySQL服务"
    exit 1
fi

# 创建必要目录
echo "创建必要目录..."
mkdir -p logs/nginx logs/mysql

# 设置目录权限
echo "设置目录权限..."
sudo chown -R $USER:$USER . 2>/dev/null || chown -R $USER:$USER .

# 停止可能运行的服务
echo "停止旧服务..."
docker-compose down --remove-orphans 2>/dev/null || true

# 构建并启动服务
echo "构建并启动服务..."
docker-compose up -d --build

# 等待服务启动
echo "等待服务启动..."
sleep 10

# 检查PHP容器
echo "检查PHP服务..."
for i in {1..30}; do
    if docker-compose ps | grep -q "9color_php.*Up"; then
        echo "PHP 服务启动成功！"
        break
    fi
    if [ $i -eq 30 ]; then
        echo "错误: PHP 服务启动超时"
        docker-compose logs php
        exit 1
    fi
    echo "等待PHP启动... ($i/30)"
    sleep 2
done

# 检查Nginx容器
echo "检查Nginx服务..."
for i in {1..30}; do
    if docker-compose ps | grep -q "9color_nginx.*Up"; then
        echo "Nginx 服务启动成功！"
        break
    fi
    if [ $i -eq 30 ]; then
        echo "错误: Nginx 服务启动超时"
        docker-compose logs nginx
        exit 1
    fi
    echo "等待Nginx启动... ($i/30)"
    sleep 2
done

# 检查MySQL容器
echo "检查MySQL服务..."
for i in {1..60}; do
    if docker-compose exec mysql mysqladmin ping -h localhost --silent 2>/dev/null; then
        echo "MySQL 服务启动成功！"
        break
    fi
    if [ $i -eq 60 ]; then
        echo "错误: MySQL 启动超时"
        docker-compose logs mysql
        exit 1
    fi
    echo "等待MySQL启动... ($i/60)"
    sleep 3
done

# 测试网站访问
echo "测试网站访问..."
sleep 5
if curl -s -o /dev/null -w "%{http_code}" http://localhost | grep -q "200\|404\|500"; then
    echo "网站访问测试成功！"
else
    echo "警告: 网站可能无法正常访问，请检查PHP配置"
fi

# 获取服务器IP
SERVER_IP=$(curl -s ifconfig.me 2>/dev/null || curl -s ipinfo.io/ip 2>/dev/null || echo "服务器IP")

echo ""
echo "=== 部署完成 ==="
echo "服务状态:"
docker-compose ps

echo ""
echo "访问信息:"
echo "  网站地址: http://$SERVER_IP"
echo "  本地访问: http://localhost"

echo ""
echo "数据库连接信息:"
echo "  主机: $SERVER_IP (外部) 或 mysql (容器内)"
echo "  端口: 3306"
echo "  数据库: 6ui"
echo "  Root密码: root123456"
echo "  应用用户: appuser"
echo "  应用密码: app123456"

echo ""
echo "常用管理命令:"
echo "  查看日志: docker-compose logs -f"
echo "  停止服务: docker-compose down"
echo "  重启服务: docker-compose restart"
echo "  进入MySQL: docker-compose exec mysql mysql -u root -p"

echo ""
echo "如有问题，请查看日志文件："
echo "  Nginx日志: ./logs/nginx/"
echo "  MySQL日志: ./logs/mysql/" 