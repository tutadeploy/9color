#!/bin/bash

# 9color项目权限修复脚本
# 修复PHP容器内文件权限问题，解决"Permission denied"错误

set -e # 遇到错误立即退出

# 服务器配置
APP_SERVER="38.180.189.204"
APP_PASSWORD="ikJ234urdq"
CONTAINER_NAME="9color_php73_prod"

# 颜色输出
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  9color 权限修复脚本${NC}"
echo -e "${GREEN}========================================${NC}"

# 检查sshpass是否安装
if ! command -v sshpass &>/dev/null; then
    echo -e "${RED}错误: sshpass 未安装，请先安装 sshpass${NC}"
    exit 1
fi

echo -e "${YELLOW}正在连接应用服务器: ${APP_SERVER}${NC}"

# 检查服务器连接
if ! sshpass -p "${APP_PASSWORD}" ssh -o ConnectTimeout=10 -o StrictHostKeyChecking=no root@${APP_SERVER} "echo 'connection test'" &>/dev/null; then
    echo -e "${RED}错误: 无法连接到应用服务器 ${APP_SERVER}${NC}"
    exit 1
fi

echo -e "${GREEN}✓ 应用服务器连接成功${NC}"

# 检查Docker容器
echo -e "${YELLOW}检查PHP容器状态...${NC}"
CONTAINER_STATUS=$(sshpass -p "${APP_PASSWORD}" ssh -o StrictHostKeyChecking=no root@${APP_SERVER} "docker ps --format 'table {{.Names}}\t{{.Status}}' | grep ${CONTAINER_NAME}" || echo "")

if [[ -z "$CONTAINER_STATUS" ]]; then
    echo -e "${RED}错误: PHP容器 ${CONTAINER_NAME} 未运行${NC}"
    exit 1
fi

echo -e "${GREEN}✓ PHP容器运行正常${NC}"
echo "  $CONTAINER_STATUS"

# 修复权限函数
fix_permissions() {
    local path=$1
    local description=$2

    echo -e "${YELLOW}修复 ${description}...${NC}"

    sshpass -p "${APP_PASSWORD}" ssh -o StrictHostKeyChecking=no root@${APP_SERVER} \
        "docker exec ${CONTAINER_NAME} sh -c \"
            if [ -e '${path}' ]; then
                chown -R www-data:www-data '${path}'
                echo '✓ ${path} 权限已修复'
            else
                mkdir -p '${path}'
                chown -R www-data:www-data '${path}'
                chmod 755 '${path}'
                echo '✓ ${path} 目录已创建并设置权限'
            fi
        \""
}

# 修复各个目录和文件的权限
echo -e "${YELLOW}开始修复文件权限...${NC}"

# 重要目录列表
declare -A PATHS=(
    ["/var/www/html/config"]="配置文件目录"
    ["/var/www/html/runtime"]="运行时缓存目录"
    ["/var/www/html/log"]="日志文件目录"
    ["/var/www/html/public/upload"]="文件上传目录"
    ["/var/www/html/public/static"]="静态资源目录"
    ["/var/www/html/php-temp"]="PHP临时文件目录"
)

# 逐个修复权限
for path in "${!PATHS[@]}"; do
    fix_permissions "$path" "${PATHS[$path]}"
done

# 特殊处理：确保特定文件的权限
echo -e "${YELLOW}修复关键配置文件权限...${NC}"

CRITICAL_FILES=(
    "/var/www/html/config/app.php"
    "/var/www/html/config/database.php"
    "/var/www/html/config/session.php"
    "/var/www/html/config/cookie.php"
)

for file in "${CRITICAL_FILES[@]}"; do
    sshpass -p "${APP_PASSWORD}" ssh -o StrictHostKeyChecking=no root@${APP_SERVER} \
        "docker exec ${CONTAINER_NAME} sh -c \"
            if [ -f '${file}' ]; then
                chown www-data:www-data '${file}'
                chmod 644 '${file}'
                echo '✓ ${file} 权限已修复'
            fi
        \""
done

# 验证权限修复结果
echo -e "${YELLOW}验证权限修复结果...${NC}"

sshpass -p "${APP_PASSWORD}" ssh -o StrictHostKeyChecking=no root@${APP_SERVER} \
    "docker exec ${CONTAINER_NAME} sh -c \"
        echo '=== 关键目录权限检查 ==='
        ls -la /var/www/html/ | grep -E '(config|runtime|log)' || echo '目录不存在'
        echo ''
        echo '=== 关键文件权限检查 ==='
        ls -la /var/www/html/config/app.php 2>/dev/null || echo 'app.php 不存在'
    \""

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  权限修复完成！${NC}"
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}✓ 所有目录和文件权限已修复为 www-data:www-data${NC}"
echo -e "${GREEN}✓ PHP应用现在应该可以正常写入配置文件${NC}"
echo -e "${YELLOW}注意: 如果问题仍然存在，请检查Nginx配置或PHP-FPM配置${NC}"
