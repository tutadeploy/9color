# 9Color 生产环境一键部署方案

## 🎯 项目说明

本方案使用 Docker Compose 一键部署 9Color 项目的完整生产环境，包含：

- ✅ PHP 8.1-FPM 应用服务
- ✅ Nginx 反向代理和静态文件服务
- ✅ MySQL 8.0 数据库服务
- ✅ 自动导入数据库文件
- ✅ 生产级配置优化
- ✅ 数据持久化存储
- ✅ 完整的日志管理

## 📁 目录结构

```
docker-deploy/
├── docker-compose.yml     # Docker Compose 配置文件
├── deploy.sh             # 一键部署脚本 ⭐
├── README.md             # 说明文档
├── nginx-config/         # Nginx 配置目录
│   ├── nginx.conf        # 主配置文件
│   └── default.conf      # 站点配置文件
├── php-config/           # PHP 配置目录
│   ├── php.ini           # PHP 配置文件
│   └── www.conf          # PHP-FPM 配置文件
├── mysql-config/         # MySQL 配置目录
│   └── my.cnf            # MySQL 配置文件
├── mysql-init/           # MySQL 初始化脚本目录
│   ├── 00-create-database.sql
│   └── 01-init.sql       # 从 数据库.sql 复制而来
├── mysql-data/           # MySQL 数据存储目录（自动创建）
└── logs/                 # 日志目录
    ├── nginx/            # Nginx 日志
    └── mysql/            # MySQL 日志
```

## 🚀 一键部署

### 部署命令

```bash
# 进入部署目录
cd docker-deploy

# 运行一键部署脚本
./deploy.sh
```

### 部署过程

脚本会自动完成以下操作：

1. **环境检查**：检查 Ubuntu 系统兼容性
2. **依赖安装**：自动安装 Docker 和 Docker Compose
3. **端口检查**：检查 80、3306 端口是否被占用
4. **目录创建**：创建必要的日志和数据目录
5. **权限设置**：设置正确的文件权限
6. **服务启动**：启动 PHP、Nginx、MySQL 服务
7. **健康检查**：验证各服务启动状态
8. **访问测试**：测试网站是否可以正常访问

## 🔧 服务配置

### 网站访问

- **生产地址**: `http://服务器IP`
- **本地测试**: `http://localhost`

### 数据库连接

- **主机**: 服务器 IP (外部访问) 或 `mysql` (容器内访问)
- **端口**: `3306`
- **数据库**: `6ui`
- **Root 密码**: `root123456`
- **应用用户**: `appuser`
- **应用密码**: `app123456`

## 📝 管理命令

```bash
# 查看服务状态
docker-compose ps

# 查看所有日志
docker-compose logs -f

# 查看特定服务日志
docker-compose logs -f nginx
docker-compose logs -f php
docker-compose logs -f mysql

# 重启所有服务
docker-compose restart

# 重启特定服务
docker-compose restart nginx

# 停止所有服务
docker-compose down

# 完全清理（删除数据）
docker-compose down -v
rm -rf mysql-data/

# 进入容器
docker-compose exec nginx sh
docker-compose exec php bash
docker-compose exec mysql bash

# 直接连接 MySQL
docker-compose exec mysql mysql -u root -p
```

## 🔍 故障排除

### 1. 端口被占用

```bash
# 检查端口占用
sudo netstat -tuln | grep -E "(80|3306)"

# 停止占用端口的服务
sudo systemctl stop nginx    # 停止系统Nginx
sudo systemctl stop mysql    # 停止系统MySQL
sudo systemctl stop apache2  # 停止Apache
```

### 2. 权限问题

```bash
# 修复目录权限
sudo chown -R $USER:$USER ./
```

### 3. 服务启动失败

```bash
# 查看详细错误日志
docker-compose logs 服务名

# 重新构建容器
docker-compose up -d --build --force-recreate
```

### 4. 网站无法访问

```bash
# 检查Nginx配置
docker-compose exec nginx nginx -t

# 检查PHP-FPM状态
docker-compose exec php php-fpm -t

# 查看错误日志
tail -f logs/nginx/error.log
```

## 🔒 安全建议

1. **修改默认密码**：部署后立即修改数据库密码
2. **防火墙配置**：
   ```bash
   sudo ufw allow 22    # SSH
   sudo ufw allow 80    # HTTP
   sudo ufw allow 443   # HTTPS (如需要)
   sudo ufw enable
   ```
3. **SSL 证书**：生产环境建议配置 HTTPS
4. **定期备份**：备份 `mysql-data/` 目录

## 📊 性能监控

### 系统资源监控

```bash
# 查看容器资源使用
docker stats

# 查看磁盘使用
df -h

# 查看内存使用
free -h
```

### 应用性能

- **Nginx 访问日志**: `logs/nginx/access.log`
- **Nginx 错误日志**: `logs/nginx/error.log`
- **MySQL 慢查询**: `logs/mysql/slow.log`

## 🆕 版本要求

- **操作系统**: Ubuntu 20.04 / 22.04
- **Docker**: 20.10+
- **Docker Compose**: 1.29+
- **服务器内存**: 建议 2GB+
- **磁盘空间**: 建议 10GB+

## 📞 技术支持

部署过程中如遇问题：

1. **查看部署日志**：脚本会显示详细的部署过程
2. **检查服务状态**：使用 `docker-compose ps` 查看服务状态
3. **查看错误日志**：检查 `logs/` 目录下的日志文件
4. **重新部署**：停止服务后重新运行 `./deploy.sh`

常见问题解决方案已在故障排除章节详细说明。
