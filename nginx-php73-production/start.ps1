# 启动项目脚本 - Windows PowerShell版本
Write-Host "正在启动 9color 项目..." -ForegroundColor Green

# 检查Docker是否运行
Write-Host "检查Docker状态..." -ForegroundColor Yellow
try {
    docker version | Out-Null
    Write-Host "Docker运行正常" -ForegroundColor Green
} catch {
    Write-Host "Docker未运行，请先启动Docker Desktop" -ForegroundColor Red
    exit 1
}

# 停止现有容器
Write-Host "停止现有容器..." -ForegroundColor Yellow
docker-compose down

# 构建并启动容器
Write-Host "构建并启动容器..." -ForegroundColor Yellow
docker-compose up -d --build

# 检查容器状态
Write-Host "检查容器状态..." -ForegroundColor Yellow
docker-compose ps

Write-Host "项目启动完成！访问 http://localhost" -ForegroundColor Green
Write-Host "MySQL端口: 3307" -ForegroundColor Cyan
Write-Host "查看日志: docker-compose logs -f" -ForegroundColor Cyan 