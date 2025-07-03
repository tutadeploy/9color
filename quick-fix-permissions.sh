#!/bin/bash
# 9color 快速权限修复脚本

echo "🔧 正在修复9color项目权限..."

sshpass -p 'ikJ234urdq' ssh -o StrictHostKeyChecking=no root@38.180.189.204 \
    "docker exec 9color_php73_prod sh -c '
        # 修复主要目录权限
        chown -R www-data:www-data /var/www/html/config/ /var/www/html/runtime/
        
        # 创建并修复log目录
        mkdir -p /var/www/html/log
        chown -R www-data:www-data /var/www/html/log
        
        # 修复upload目录权限
        mkdir -p /var/www/html/public/upload
        chown -R www-data:www-data /var/www/html/public/upload
        
        # 修复临时目录
        mkdir -p /var/www/html/php-temp
        chown -R www-data:www-data /var/www/html/php-temp
        
        echo "✅ 权限修复完成"
        echo "关键文件权限检查:"
        ls -la /var/www/html/config/app.php
    '"

echo "🎉 权限修复脚本执行完成！"
