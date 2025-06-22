# 9Color 数据库服务器

独立的数据库服务器，包含完整的9Color系统数据库结构和数据。

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
   - Root密码: `root123456`

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

### 查看MySQL日志
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
├── start.sh                    # 智能启动脚本
├── docker-compose.yml          # Ubuntu22配置
├── docker-compose-m1.yml       # M1 Mac配置
├── mysql/
│   ├── my.cnf                  # MySQL配置
│   ├── 00-create-database.sql  # 数据库初始化
│   └── 02-import-data.sql      # 数据导入
├── phpmyadmin/                 # phpMyAdmin配置
├── scripts/                    # 备份脚本
├── logs/                       # 日志目录
└── backup/                     # 备份目录
```

## 🔧 配置说明

### MySQL配置优化
- 字符集: utf8mb4
- 连接池: 300
- 缓冲池: 512M (M1) / 768M (Ubuntu22)
- 二进制日志: 启用，保留7天

### 安全配置
- 绑定所有接口
- 密码认证
- 权限分离

## ⚠️ 注意事项

1. **端口占用**: 确保 3306 和 8090 端口未被占用
2. **数据持久化**: 数据存储在Docker卷中，删除容器不会丢失数据
3. **首次启动**: 数据导入可能需要几分钟时间
4. **M1兼容**: M1 Mac会自动使用 `linux/amd64` 平台

## 🆘 故障排除

### MySQL启动失败
```bash
# 查看详细日志
docker logs 9color_mysql_standalone

# 检查端口占用
lsof -i :3306
```

### 数据导入失败
```bash
# 重新初始化（会清空数据）
docker-compose down -v
./start.sh
```

### phpMyAdmin无法访问
```bash
# 检查容器状态
docker ps | grep phpmyadmin

# 查看phpMyAdmin日志
docker logs 9color_phpmyadmin
```

## 监控信息

### 资源使用
```bash
# 查看详细状态
docker ps
```

### 容器状态
```bash
# 查看所有容器
docker ps

# 查看资源使用
docker stats
```

## 安全注意事项

1. **防火墙设置**: 确保只开放必要端口
2. **密码安全**: 定期更换数据库密码
3. **备份验证**: 定期验证备份文件完整性
4. **访问控制**: 限制phpMyAdmin访问IP

## 维护计划

### 日常维护
- 检查服务状态
- 监控资源使用
- 查看错误日志

### 周期维护
- 验证备份完整性
- 清理旧日志文件
- 更新系统补丁

## 联系信息
如有问题，请联系系统管理员。

---
*文档版本: 1.0*  
*更新时间: 2025-06-19* 