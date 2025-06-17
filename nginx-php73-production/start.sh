#!/bin/bash

# 9Color PHP 7.3 生产环境启动脚本
echo "🚀 启动 9Color PHP 7.3 生产环境..."

# 创建日志目录
mkdir -p logs/nginx logs/php-fpm logs/mysql

# 检查Docker是否运行
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker 未运行，请先启动 Docker"
    exit 1
fi

# 停止并清理现有容器
echo "🧹 清理现有容器..."
docker-compose down --remove-orphans

# 构建和启动服务
echo "🔨 构建 PHP 镜像..."
docker-compose build --no-cache php-fpm

echo "🚀 启动所有服务..."
docker-compose up -d

# 等待服务启动
echo "⏳ 等待服务启动..."
sleep 10

# 检查服务状态
echo "📊 检查服务状态..."
docker-compose ps

# 显示访问信息
echo ""
echo "✅ 服务启动完成！"
echo ""
echo "📱 访问信息："
echo "   网站地址: http://localhost:8080"
echo "   数据库地址: localhost:3306"
echo "   数据库用户: root / root123456"
echo "   应用数据库: 6ui"
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
sleep 5
if curl -s http://localhost:8080 > /dev/null; then
    echo "✅ 网站可以正常访问"
else
    echo "⚠️  网站可能还在启动中，请稍后再试"
fi

echo ""
echo "🎉 部署完成！祝你使用愉快！" 