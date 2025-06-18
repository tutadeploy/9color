#!/bin/bash

# 9Color PHP 7.3 生产环境启动脚本 - Ubuntu 22 版本
echo "🚀 启动 9Color 项目..."

# 检查Docker是否运行
echo "🔍 检查Docker状态..."
if ! docker version > /dev/null 2>&1; then
    echo "❌ Docker未运行，请先启动Docker"
    exit 1
fi
echo "✅ Docker运行正常"

# 停止现有容器
echo "🛑 停止现有容器..."
docker-compose down

# 构建并启动容器
echo "🔨 构建并启动容器..."
docker-compose up -d --build

# 等待服务启动
echo "⏳ 等待服务启动..."
sleep 5

# 检查容器状态
echo "📊 检查容器状态..."
docker-compose ps

echo ""
echo "✅ 项目启动完成！"
echo ""
echo "📱 访问信息："
echo "   前台地址: http://localhost"
echo "   后台地址: http://localhost/admin"
echo "   MySQL端口: 3306"
echo ""
echo "🔐 登录信息："
echo "   后台账号: admin / sgcpj123"
echo "   前台测试: test / (需要尝试常见密码)"
echo ""
echo "🔧 管理命令："
echo "   查看日志: docker-compose logs -f [服务名]"
echo "   重启服务: docker-compose restart [服务名]"
echo "   停止服务: docker-compose down"
echo "   进入容器: docker exec -it [容器名] /bin/sh"
echo ""
echo "📋 容器信息："
echo "   Nginx: 9color_nginx_prod"
echo "   PHP-FPM: 9color_php73_prod"
echo "   MySQL: 9color_mysql_prod"
echo ""

# 测试网站连通性
echo "🔍 测试网站连通性..."
sleep 3
if curl -s -o /dev/null -w "%{http_code}" http://localhost | grep -q "200\|301\|302"; then
    echo "✅ 网站可以正常访问"
else
    echo "⚠️  网站可能还在启动中，请稍后再试"
    echo "💡 可以使用以下命令查看日志："
    echo "   docker-compose logs nginx"
    echo "   docker-compose logs php-fpm"
fi

echo ""
echo "🎉 部署完成！祝你使用愉快！" 