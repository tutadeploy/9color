# 9Color 数据库服务器管理指南

## 服务器信息
- **服务器IP**: 38.180.150.127
- **用户名**: root
- **密码**: HVN8jqpfPB
- **配置**: 4GB RAM, 40GB SSD, 3核CPU

## 服务组件
- **MySQL 5.7**: 主数据库服务
- **自动备份服务**: 定时备份数据库
- **phpMyAdmin**: Web数据库管理界面

## 快速开始

### 1. 启动所有服务
```bash
cd /root/database-server
./manage.sh start
```

### 2. 查看服务状态
```bash
./manage.sh status
```

### 3. 访问phpMyAdmin
浏览器访问: http://38.180.150.127:8090

## 管理命令

### 基本命令
```bash
# 启动所有服务
./manage.sh start

# 停止所有服务
./manage.sh stop

# 重启所有服务
./manage.sh restart

# 查看服务状态
./manage.sh status

# 显示帮助
./manage.sh help
```

### 日志查看
```bash
# 查看所有服务日志
./manage.sh logs

# 查看MySQL日志
./manage.sh logs mysql

# 查看备份服务日志
./manage.sh logs backup

# 查看phpMyAdmin日志
./manage.sh logs phpmyadmin
```

### 维护操作
```bash
# 手动执行备份
./manage.sh backup

# 单独重启phpMyAdmin
./manage.sh phpmyadmin

# 清理停止的容器
./manage.sh clean
```

## 数据库连接信息

### 连接参数
- **主机**: 38.180.150.127
- **端口**: 3306
- **数据库**: 6ui

### 用户账户
| 用户名 | 密码 | 权限 | 用途 |
|--------|------|------|------|
| root | root123456 | 超级管理员 | 完全访问权限 |
| app | app123456 | 应用用户 | 读写权限 |
| readonly | readonly123456 | 只读用户 | 查询权限 |
| backup | backup123456 | 备份用户 | 备份权限 |

## 备份策略

### 自动备份
- **完整备份**: 每天凌晨2点
- **增量备份**: 每6小时一次
- **保留策略**: 完整备份保留5天，二进制日志保留2天

### 备份位置
- **容器内**: `/backup`
- **主机**: `/root/database-server/backup`

### 手动备份
```bash
./manage.sh backup
```

## 故障排除

### phpMyAdmin无法访问
```bash
# 重启phpMyAdmin
./manage.sh phpmyadmin

# 检查日志
./manage.sh logs phpmyadmin
```

### MySQL连接问题
```bash
# 检查MySQL状态
./manage.sh logs mysql

# 重启MySQL
./manage.sh restart
```

### Session Cookie错误
phpMyAdmin已配置支持HTTP访问，如果仍有问题：
1. 清除浏览器缓存和Cookie
2. 重启phpMyAdmin: `./manage.sh phpmyadmin`

## 监控信息

### 资源使用
```bash
# 查看详细状态
./manage.sh status
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