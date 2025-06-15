#!/bin/bash

echo "Starting 9color PHP Development Environment..."

# 启动Apache
apache2-foreground &

# 启动PHP内置服务器（用于快速开发）
echo "Starting PHP built-in server on port 8080 with custom router..."
cd /var/www/html
php -S 0.0.0.0:8080 -t public public/router.php &

# 可选：启动第二个PHP服务器在8000端口（如果需要）
if [ "$ENABLE_PORT_8000" = "true" ]; then
    echo "Starting additional PHP server on port 8000..."
    php -S 0.0.0.0:8000 -t public public/router.php &
fi

# 保持容器运行
wait 