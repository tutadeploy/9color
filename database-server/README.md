# 9Color 独立数据库服务器

这是 9Color 电商平台的独立数据库服务器配置，包含完整的数据库初始化脚本和配置。

## 🚀 快速启动

### 一键启动（推荐）

```bash
# 给启动脚本执行权限
chmod +x start.sh

# 启动数据库服务器
./start.sh
```

启动脚本会自动：

- 检测运行环境（M1 Mac / Ubuntu22）
- 选择对应的配置文件
- 启动所有必要的服务
- 导入完整的数据库结构和数据

## 🏗️ 服务架构

### 包含的服务

1. **MySQL 5.7** - 主数据库服务

   - 端口: 3306
   - 数据库: `6ui`
   - 用户: `app` / 密码: `app123456`
   - Root 密码: `root123456`

2. **phpMyAdmin** - 数据库管理界面

   - 端口: 8090
   - 访问: http://localhost:8090

3. **备份服务** - 自动数据库备份

### 环境适配

- **M1 Mac**: 使用 `docker-compose-m1.yml`（包含 `platform: linux/amd64`）
- **Ubuntu22**: 使用 `docker-compose.yml`（标准配置）

## 📊 数据库信息

### 连接信息

```
主机: localhost
端口: 3306
数据库: 6ui
用户名: app
密码: app123456
```

### 用户权限

- `app@%`: 应用用户，拥有数据库完整权限
- `readonly@%`: 只读用户，用于报表查询
- `backup@%`: 备份用户，用于数据备份

## 🗃️ 数据导入

数据库启动时会自动执行以下初始化：

1. `00-create-database.sql` - 创建数据库和用户
2. `02-import-data.sql` - 导入完整的表结构和数据

## 🛠️ 管理命令

### 查看服务状态

```bash
docker ps
```

### 查看 MySQL 日志

```bash
docker logs 9color_mysql_standalone
```

### 停止服务

```bash
# M1 Mac
docker-compose -f docker-compose-m1.yml down

# Ubuntu22
docker-compose -f docker-compose.yml down
```

### 重启服务

```bash
./start.sh
```

## 📁 目录结构

```
database-server/
├── docker-compose.yml          # Docker编排配置
├── docker-compose-m1.yml       # M1芯片Mac专用配置
├── README.md                   # 说明文档
├── mysql/
│   ├── 00-complete-init.sql    # 统一数据库初始化脚本 ⭐
│   └── my.cnf                  # MySQL配置文件
├── backup/                     # 数据库备份目录
├── logs/                       # 日志目录
├── phpmyadmin/                 # phpMyAdmin配置
└── scripts/                    # 维护脚本
```

## 🚀 快速部署

### 1. 启动数据库服务器

```bash
# 进入数据库服务器目录
cd database-server

# 启动服务（首次启动会自动初始化数据库）
docker-compose up -d

# 查看启动状态
docker-compose ps
```

### 2. 验证部署

```bash
# 检查数据库连接
mysql -h localhost -P 3306 -u app -papp123456 6ui -e "SHOW TABLES;"

# 检查管理员账号
mysql -h localhost -P 3306 -u app -papp123456 6ui -e "SELECT username FROM system_user WHERE username='admin';"
```

## 📋 统一初始化脚本说明

### `00-complete-init.sql` 包含内容：

1. **数据库创建**：创建`6ui`数据库，设置 utf8mb4 字符集
2. **用户权限**：创建`app`、`readonly`、`backup`三个用户并授权
3. **完整表结构**：所有业务表的 DDL 语句
4. **初始数据**：系统配置、管理员账号、权限设置等
5. **自动派单功能**：相关表和存储过程（已修复返佣金额记录问题）
6. **索引优化**：所有必要的索引和约束

### 默认账号信息：

| 类型         | 用户名   | 密码           | 权限               |
| ------------ | -------- | -------------- | ------------------ |
| 数据库管理员 | root     | root123456     | 全部权限           |
| 应用用户     | app      | app123456      | 6ui 数据库全部权限 |
| 只读用户     | readonly | readonly123456 | 6ui 数据库只读权限 |
| 备份用户     | backup   | backup123456   | 备份相关权限       |
| 后台管理员   | admin    | admin123456    | 后台系统超级管理员 |

## 🔧 环境变量配置

可以通过环境变量自定义配置：

```bash
# 数据库root密码
MYSQL_ROOT_PASSWORD=your_root_password

# 数据库名
MYSQL_DATABASE=6ui

# 应用用户名和密码
MYSQL_USER=app
MYSQL_PASSWORD=your_app_password
```

## 📊 服务组件

### MySQL 5.7

- **端口**：3306
- **数据持久化**：使用 Docker Volume
- **配置文件**：`mysql/my.cnf`
- **初始化脚本**：`mysql/00-complete-init.sql`

### phpMyAdmin

- **访问地址**：http://your-server-ip:8090
- **用户名**：app 或 root
- **密码**：对应的数据库密码

### 自动备份服务

- **备份目录**：`./backup/`
- **备份脚本**：`scripts/backup-scheduler.sh`
- **备份频率**：可配置

## 🛠️ 维护操作

### 查看日志

```bash
# 查看MySQL日志
docker-compose logs mysql

# 查看备份服务日志
docker-compose logs mysql-backup

# 查看phpMyAdmin日志
docker-compose logs phpmyadmin
```

### 手动备份

```bash
# 进入备份容器
docker exec -it 9color_mysql_backup bash

# 执行备份
/scripts/backup-scheduler.sh
```

### 重新初始化数据库

```bash
# 停止服务
docker-compose down

# 删除数据卷（⚠️ 会丢失所有数据）
docker volume rm database-server_mysql_data

# 重新启动（会重新初始化）
docker-compose up -d
```

## 🔒 安全建议

1. **修改默认密码**：部署后立即修改所有默认密码
2. **网络安全**：配置防火墙，只允许必要的端口访问
3. **定期备份**：确保备份服务正常运行
4. **监控日志**：定期检查数据库和应用日志
5. **版本更新**：定期更新 MySQL 和相关组件

## 📝 数据迁移

### 从现有数据库迁移

如果需要从其他数据库迁移数据，可以：

1. 导出现有数据：

```bash
mysqldump -h source_host -u user -p source_db > migration.sql
```

2. 导入到新数据库：

```bash
mysql -h localhost -P 3306 -u app -papp123456 6ui < migration.sql
```

## 🆘 故障排除

### 常见问题

1. **容器启动失败**

   - 检查端口是否被占用
   - 查看 Docker 日志：`docker-compose logs`

2. **数据库连接失败**

   - 确认容器状态：`docker-compose ps`
   - 检查网络配置和防火墙设置

3. **初始化脚本执行失败**

   - 查看 MySQL 日志中的错误信息
   - 检查 SQL 脚本语法

4. **phpMyAdmin 无法访问**
   - 确认端口 8090 未被占用
   - 检查防火墙设置

## 📞 技术支持

如有问题，请提供以下信息：

- 系统环境（操作系统、Docker 版本）
- 错误日志（`docker-compose logs`）
- 配置文件内容
- 具体的错误现象描述

## 🔄 更新记录

### v1.1 - 2025-06-23

- **修复自动派单返佣金额记录问题**
  - 问题：自动派单中返佣记录（type=3）只记录佣金金额，而不是商品价格+佣金
  - 修复：修改 `ProcessAutoDispatchOrder` 存储过程，将返佣记录从 `v_commission` 改为 `v_amount + v_commission`
  - 影响：确保返佣信息的金额记录与手动结算保持一致
- **修复数据库备份权限问题**
  - 问题：app 用户执行 `mysqldump --routines` 时提示缺少 PROCESS 权限
  - 修复：为 `app` 和 `backup` 用户添加 `PROCESS` 权限和 `mysql.proc` 表的 `SELECT` 权限
  - 影响：现在可以正常备份存储过程和函数

### v1.0 - 2025-06-22

- 初始版本：统一数据库初始化脚本
- 包含完整的表结构、初始数据和自动派单功能

---

**最后更新**：2025-06-23  
**版本**：v1.1 - 修复自动派单返佣金额记录
