#!/bin/bash

# 数据库服务器完整启动脚本
# 包含 MySQL、备份服务、phpMyAdmin

echo "======================================="
echo "        9Color 数据库服务器启动"
echo "======================================="
echo "时间: $(date)"
echo "服务器: $(hostname -I | awk '{print $1}')"
echo ""

# 检查Docker是否运行
if ! docker info >/dev/null 2>&1; then
    echo "❌ Docker未运行，请先启动Docker服务"
    exit 1
fi

echo "✅ Docker服务正常"

# 进入脚本目录
cd "$(dirname "$0")"

echo ""
echo "=== 1. 启动MySQL和备份服务 ==="

# 使用docker compose启动主要服务
docker compose up -d mysql mysql-backup

echo "等待MySQL启动完成..."
sleep 10

# 检查MySQL状态
if docker ps | grep -q "9color_mysql_standalone"; then
    echo "✅ MySQL服务启动成功"
else
    echo "❌ MySQL服务启动失败"
    docker logs 9color_mysql_standalone --tail 20
    exit 1
fi

# 检查备份服务状态
if docker ps | grep -q "9color_mysql_backup"; then
    echo "✅ 备份服务启动成功"
else
    echo "❌ 备份服务启动失败"
    docker logs 9color_mysql_backup --tail 20
fi

echo ""
echo "=== 2. 启动phpMyAdmin ==="

# 运行phpMyAdmin启动脚本
if [ -f "./start-phpmyadmin.sh" ]; then
    chmod +x ./start-phpmyadmin.sh
    ./start-phpmyadmin.sh
else
    echo "❌ phpMyAdmin启动脚本不存在"
fi

echo ""
echo "=== 3. 服务状态总览 ==="

echo ""
echo "运行中的容器:"
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo ""
echo "系统资源使用:"
echo "内存使用情况:"
free -h

echo ""
echo "磁盘使用情况:"
df -h /

echo ""
echo "Docker容器资源使用:"
docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.NetIO}}\t{{.BlockIO}}"

echo ""
echo "=== 4. 服务访问信息 ==="
SERVER_IP=$(hostname -I | awk '{print $1}')

echo "🔗 服务访问地址:"
echo "  📊 phpMyAdmin:    http://$SERVER_IP:8090"
echo "  🗄️  MySQL端口:    $SERVER_IP:3306"
echo ""
echo "👤 数据库用户:"
echo "  🔑 root          / root123456     (超级管理员)"
echo "  💼 app           / app123456      (应用用户)"
echo "  👁️  readonly      / readonly123456 (只读用户)"
echo "  💾 backup        / backup123456   (备份用户)"
echo ""
echo "📁 数据存储:"
echo "  🗂️  数据目录:     /var/lib/mysql (容器内)"
echo "  💾 备份目录:     /backup"
echo "  📋 日志目录:     ./logs/mysql"

echo ""
echo "=== 启动完成 ==="
echo "所有服务已启动完成，可以开始使用数据库服务器"
echo "======================================="
