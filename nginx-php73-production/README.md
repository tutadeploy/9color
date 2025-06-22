d# 9Color PHP 7.3 生产环境部署方案

## ⚠️ 重要变更说明

**数据库架构调整**：
- 本项目不再包含数据库服务，数据库已独立部署
- 请先启动独立数据库服务器：`../database-server/start.sh`
- 本地开发环境会自动连接到 localhost:3306

## ⚠️ 重要变更说明

**数据库架构调整**：
- 本项目不再包含数据库服务，数据库已独立部署
- 请先启动独立数据库服务器：`../database-server/start.sh`
- 本地开发环境会自动连接到 localhost:3306

## ⚠️ 重要变更说明

**数据库架构调整**：
- 本项目不再包含数据库服务，数据库已独立部署
- 请先启动独立数据库服务器：`../database-server/start.sh`
- 本地开发环境会自动连接到 localhost:3306

## 🎯 方案概述

这是一个基于 **Nginx + PHP-FPM 7.3** 的生产级部署方案，专门为PHP 7.3项目优化设计。

### 🏗️ 架构特点

- **高性能**: 使用 Nginx + PHP-FPM 组合，性能优于 Apache + mod_php
- **资源优化**: PHP-FPM进程池动态管理，内存使用更高效
- **生产就绪**: 包含完整的日志、监控、安全配置
- **易于扩展**: 支持水平扩展和负载均衡

## 📋 系统要求

- Docker >= 20.0
- Docker Compose >= 1.29
- 至少 4GB 可用内存
- 至少 10GB 可用磁盘空间

## 🚀 快速启动

### 1. 启动数据库服务器（必须先执行）

```bash
# 进入数据库服务器目录
cd ../database-server

# 启动独立数据库服务器
./start.sh
```

### 2. 启动应用服务

```bash
# 返回应用目录
cd ../nginx-php73-production

# 启动应用服务（一键启动）
./manage.sh 1
```

### 3. 访问应用

- **本地开发**: http://localhost:9080 (M1 Mac)
- **生产环境**: http://localhost:80 (Ubuntu22)
- **数据库**: localhost:3306 (由独立数据库服务器提供)

## 🔧 配置说明

### PHP-FPM 配置

位置: `php/php-fpm.d/www.conf`

关键配置项：
```ini
pm = dynamic                 # 动态进程管理
pm.max_children = 24        # 最大子进程数（适合4GB内存）
pm.start_servers = 6        # 启动时进程数
pm.min_spare_servers = 4    # 最小空闲进程
pm.max_spare_servers = 12   # 最大空闲进程
pm.max_requests = 500       # 进程处理请求数后重启
```

### Nginx 配置

位置: `nginx/conf.d/default.conf`

特性：
- ThinkPHP URL重写支持
- 静态文件缓存优化
- 安全头设置
- FastCGI缓冲优化

### PHP 配置

位置: `php/php.ini`

优化项：
- OPcache 启用和优化
- 内存限制: 256M
- 上传文件大小: 64M
- 错误日志记录

## 📊 监控和日志

### 查看日志

```bash
# 查看所有服务日志
docker-compose logs -f

# 查看特定服务日志
docker-compose logs -f nginx
docker-compose logs -f php-fpm
docker-compose logs -f mysql
```

### 监控端点

- PHP-FPM状态: http://localhost:8080/status
- PHP-FPM ping: http://localhost:8080/ping

### 日志文件位置

```
logs/
├── nginx/
│   ├── access.log
│   ├── error.log
│   ├── 9color-access.log
│   └── 9color-error.log
├── php-fpm/
│   ├── error.log
│   └── slow.log
└── mysql/
    └── slow.log
```

## 🛠️ 常用管理命令

```bash
# 查看容器状态
docker-compose ps

# 重启服务
docker-compose restart [服务名]

# 停止所有服务
docker-compose down

# 进入容器
docker exec -it 9color_nginx_prod /bin/sh
docker exec -it 9color_php73_prod /bin/sh
docker exec -it 9color_mysql_prod /bin/bash

# 查看PHP配置
docker exec -it 9color_php73_prod php -i

# 查看PHP-FPM状态
curl http://localhost:8080/status

# 数据库连接测试
docker exec -it 9color_mysql_prod mysql -uroot -proot123456
```

## ⚡ 性能优化

### 1. PHP-FPM 进程数调整

根据服务器内存调整 `pm.max_children`：
- 计算公式: (可用内存 - 1GB) / 每进程内存(128MB)
- 4GB内存服务器: 24个进程
- 8GB内存服务器: 48个进程

### 2. OPcache 优化

```ini
opcache.memory_consumption=128    # OPcache内存
opcache.max_accelerated_files=10000  # 缓存文件数
opcache.revalidate_freq=2         # 重新验证频率
```

### 3. MySQL 优化

```ini
innodb_buffer_pool_size=512M      # InnoDB缓冲池
max_connections=200               # 最大连接数
```

## 🔒 安全配置

### 1. Nginx 安全头

```nginx
add_header X-Frame-Options "SAMEORIGIN";
add_header X-XSS-Protection "1; mode=block";
add_header X-Content-Type-Options "nosniff";
```

### 2. PHP 安全设置

```ini
display_errors = Off              # 关闭错误显示
expose_php = Off                  # 隐藏PHP版本
allow_url_include = Off           # 禁止URL包含
```

### 3. 文件权限

```nginx
location ~ /\. {
    deny all;                     # 拒绝访问隐藏文件
}
```

## 🚀 部署到生产环境

### 1. 环境变量配置

创建 `.env` 文件：
```env
MYSQL_ROOT_PASSWORD=your_secure_password
MYSQL_DATABASE=your_database_name
MYSQL_USER=your_app_user
MYSQL_PASSWORD=your_app_password
```

### 2. SSL/HTTPS 配置

在 `nginx/conf.d/` 中添加SSL配置：
```nginx
server {
    listen 443 ssl http2;
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    # ... 其他配置
}
```

### 3. 反向代理配置

如果使用负载均衡器，配置真实IP获取：
```nginx
set_real_ip_from 10.0.0.0/8;
real_ip_header X-Forwarded-For;
```

## 🐛 故障排除

### 常见问题

1. **502 Bad Gateway**
   - 检查PHP-FPM是否运行: `docker-compose ps`
   - 查看PHP-FPM日志: `docker-compose logs php-fpm`

2. **内存不足**
   - 调整 `pm.max_children` 数量
   - 增加服务器内存或优化应用代码

3. **数据库连接失败**
   - 检查MySQL容器状态
   - 验证数据库配置和密码

4. **静态文件404**
   - 检查文件路径和权限
   - 确认Nginx配置中的root路径

### 日志分析

```bash
# 查看错误日志
tail -f logs/nginx/error.log
tail -f logs/php-fpm/error.log

# 查看慢查询
tail -f logs/php-fpm/slow.log
tail -f logs/mysql/slow.log
```

## 📈 扩展和优化

### 1. 添加Redis缓存

```yaml
redis:
  image: redis:7-alpine
  container_name: 9color_redis
  ports:
    - "6379:6379"
```

### 2. 多PHP-FPM实例

```yaml
php-fpm-1:
  # 第一个实例配置
php-fpm-2:
  # 第二个实例配置
```

### 3. Nginx负载均衡

```nginx
upstream php_backend {
    server php-fpm-1:9000;
    server php-fpm-2:9000;
}
```

## 📞 技术支持

如有问题，请检查：
1. Docker和Docker Compose版本
2. 系统资源使用情况
3. 日志文件中的错误信息
4. 防火墙和端口配置

---

**注意**: 这是一个针对PHP 7.3的生产级部署方案。虽然PHP 7.3已经EOL，但此方案提供了完整的安全和性能优化配置。建议在条件允许时升级到支持的PHP版本。 