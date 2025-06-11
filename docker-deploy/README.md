# 9Color 数据库 Docker 部署方案

## 🎯 项目说明

本方案使用 Docker Compose 部署 9Color 项目的 MySQL 数据库，解决了以下问题：

- ✅ 自动导入 `数据库.sql` 文件
- ✅ 永久解决 `Field 'status' doesn't have a default value` 错误
- ✅ 兼容 Ubuntu 20/22 系统
- ✅ 数据持久化存储
- ✅ 包含 phpMyAdmin 管理界面

## 📁 目录结构

```
docker-deploy/
├── docker-compose.yml     # Docker Compose 配置文件
├── deploy.sh             # 一键部署脚本
├── README.md             # 说明文档
├── mysql-config/
│   └── my.cnf            # MySQL 配置文件
├── mysql-init/           # MySQL 初始化脚本目录
│   ├── 00-create-database.sql
│   └── 01-init.sql       # 从 数据库.sql 复制而来
└── mysql-data/           # MySQL 数据存储目录（自动创建）
```

## 🚀 快速部署

### 方法 1：使用一键脚本（推荐）

```bash
# 进入部署目录
cd docker-deploy

# 运行部署脚本
./deploy.sh
```

### 方法 2：手动部署

```bash
# 1. 安装 Docker 和 Docker Compose (Ubuntu)
sudo apt update
sudo apt install -y docker.io docker-compose

# 2. 启动服务
cd docker-deploy
docker-compose up -d

# 3. 查看启动状态
docker-compose ps
```

## 🔧 配置说明

### MySQL 连接信息

- **主机**: `localhost` 或服务器 IP
- **端口**: `3306`
- **数据库**: `9color`
- **Root 密码**: `root123456`
- **应用用户**: `appuser`
- **应用密码**: `app123456`

### phpMyAdmin 访问

- **地址**: `http://localhost:8080`
- **用户名**: `root`
- **密码**: `root123456`

## 🛠️ 核心解决方案

### 1. sql_mode 配置

通过 `mysql-config/my.cnf` 文件永久设置：

```ini
sql_mode = "ONLY_FULL_GROUP_BY,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
```

### 2. 自动初始化

- `mysql-init/` 目录下的 `.sql` 文件会按文件名顺序自动执行
- `00-create-database.sql`: 创建数据库
- `01-init.sql`: 导入数据表和数据

### 3. 数据持久化

- 数据存储在 `mysql-data/` 目录
- 容器重启数据不会丢失

## 📝 常用命令

```bash
# 查看服务状态
docker-compose ps

# 查看日志
docker-compose logs -f

# 重启服务
docker-compose restart

# 停止服务
docker-compose down

# 完全清理（删除数据）
docker-compose down -v
rm -rf mysql-data/

# 进入 MySQL 容器
docker-compose exec mysql bash

# 直接连接 MySQL
docker-compose exec mysql mysql -u root -p
```

## 🔍 故障排除

### 1. 端口被占用

```bash
# 检查端口占用
sudo netstat -tuln | grep 3306

# 停止系统MySQL服务
sudo systemctl stop mysql

# 或修改 docker-compose.yml 中的端口映射
ports:
  - "3307:3306"  # 将主机端口改为3307
```

### 2. 权限问题

```bash
# 修复目录权限
sudo chown -R $USER:$USER ./mysql-data
```

### 3. 初始化失败

```bash
# 查看初始化日志
docker-compose logs mysql

# 重新初始化（会清空数据）
docker-compose down -v
rm -rf mysql-data/
docker-compose up -d
```

## 🔒 安全建议

1. **修改默认密码**：部署后请及时修改默认密码
2. **防火墙配置**：仅开放必要端口
3. **网络访问**：生产环境建议限制数据库外部访问
4. **备份策略**：定期备份 `mysql-data/` 目录

## 📊 性能优化

配置文件已包含基本优化参数：

- `max_connections = 200`
- `innodb_buffer_pool_size = 256M`
- `innodb_log_file_size = 64M`

根据服务器配置可进一步调整 `mysql-config/my.cnf`。

## 🆕 版本兼容性

- **Docker**: 20.10+
- **Docker Compose**: 1.29+
- **Ubuntu**: 20.04 / 22.04
- **MySQL**: 8.0

## 📞 技术支持

如遇问题，请检查：

1. Docker 服务是否正常运行
2. 端口是否被占用
3. 目录权限是否正确
4. 系统资源是否充足
