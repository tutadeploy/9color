#!/bin/bash

# 9Color 数据库部署脚本
# 适用于 Ubuntu 20/22 系统

set -e

echo "=== 9Color 数据库部署脚本 ==="

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
if netstat -tuln | grep -q :3306; then
    echo "错误: 端口 3306 已被占用，请停止其他MySQL服务或修改docker-compose.yml中的端口映射"
    exit 1
fi

if netstat -tuln | grep -q :8080; then
    echo "警告: 端口 8080 已被占用，phpMyAdmin将无法启动"
fi

# 设置目录权限
echo "设置目录权限..."
sudo chown -R $USER:$USER ./mysql-data ./mysql-init ./mysql-config

# 启动服务
echo "启动 Docker Compose 服务..."
docker-compose down --remove-orphans
docker-compose up -d

# 等待MySQL启动
echo "等待MySQL启动..."
for i in {1..30}; do
    if docker-compose exec mysql mysqladmin ping -h localhost --silent; then
        echo "MySQL 启动成功！"
        break
    fi
    if [ $i -eq 30 ]; then
        echo "错误: MySQL 启动超时"
        docker-compose logs mysql
        exit 1
    fi
    echo "等待中... ($i/30)"
    sleep 2
done

echo ""
echo "=== 部署完成 ==="
echo "MySQL 连接信息:"
echo "  主机: localhost (或服务器IP)"
echo "  端口: 3306"
echo "  数据库: 9color"
echo "  root密码: root123456"
echo "  应用用户: appuser"
echo "  应用密码: app123456"
echo ""
echo "phpMyAdmin 访问地址: http://localhost:8080 (如果启用)"
echo ""
echo "常用命令:"
echo "  查看日志: docker-compose logs -f"
echo "  停止服务: docker-compose down"
echo "  重启服务: docker-compose restart"
echo "  进入MySQL: docker-compose exec mysql mysql -u root -p" 