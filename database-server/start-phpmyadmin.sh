#!/bin/bash

# phpMyAdmin 启动脚本
# 用于解决容器冲突和配置更新问题

echo "=== phpMyAdmin 启动脚本 ==="
echo "时间: $(date)"

# 检查是否存在旧的phpMyAdmin容器
if docker ps -a | grep -q "9color_phpmyadmin"; then
    echo "发现现有的phpMyAdmin容器，正在停止并删除..."
    docker stop 9color_phpmyadmin 2>/dev/null || true
    docker rm 9color_phpmyadmin 2>/dev/null || true
    echo "旧容器已清理完成"
fi

# 确保网络存在
echo "检查Docker网络..."
if ! docker network ls | grep -q "database-server_db-network"; then
    echo "创建Docker网络..."
    docker network create database-server_db-network
fi

# 确保配置文件存在
if [ ! -f "/root/database-server/phpmyadmin/config.user.inc.php" ]; then
    echo "错误: phpMyAdmin配置文件不存在!"
    echo "请确保 /root/database-server/phpmyadmin/config.user.inc.php 文件存在"
    exit 1
fi

echo "配置文件检查通过"

# 启动phpMyAdmin容器
echo "正在启动phpMyAdmin容器..."
docker run -d \
    --name 9color_phpmyadmin \
    --network database-server_db-network \
    -p 8090:80 \
    -e PMA_HOST=mysql \
    -e PMA_PORT=3306 \
    -e PMA_ARBITRARY=1 \
    -e MYSQL_ROOT_PASSWORD=root123456 \
    -e UPLOAD_LIMIT=100M \
    -e MEMORY_LIMIT=512M \
    -e MAX_EXECUTION_TIME=300 \
    -e PMA_ABSOLUTE_URI=http://38.180.150.127:8090/ \
    -e SESSION_COOKIE_SAMESITE=Lax \
    -e SESSION_COOKIE_SECURE=0 \
    -v /root/database-server/phpmyadmin/config.user.inc.php:/etc/phpmyadmin/config.user.inc.php \
    -v /root/database-server/phpmyadmin/php-init.php:/usr/local/etc/php/conf.d/99-phpmyadmin-init.php \
    --restart unless-stopped \
    phpmyadmin/phpmyadmin:5.2

# 检查启动状态
sleep 3
if docker ps | grep -q "9color_phpmyadmin"; then
    echo "✅ phpMyAdmin启动成功!"
    echo "访问地址: http://$(hostname -I | awk '{print $1}'):8090"
    echo "可用用户:"
    echo "  - root / root123456 (管理员)"
    echo "  - app / app123456 (应用用户)"
    echo "  - readonly / readonly123456 (只读用户)"
    echo "  - backup / backup123456 (备份用户)"

    echo ""
    echo "容器状态:"
    docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep phpmyadmin

    echo ""
    echo "内存使用情况:"
    docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}" 9color_phpmyadmin
else
    echo "❌ phpMyAdmin启动失败!"
    echo "查看日志:"
    docker logs 9color_phpmyadmin --tail 20
    exit 1
fi

echo "=== 启动完成 ==="
