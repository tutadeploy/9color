# 9color 权限修复脚本使用说明

## 问题描述
当线上系统出现以下错误时使用：
```
file_put_contents(/var/www/html/public/../application/../config/app.php): failed to open stream: Permission denied
```

## 脚本列表

### 1. `quick-fix-permissions.sh` - 快速修复脚本 ⭐️ 推荐
**一键解决权限问题，简单快速**

```bash
# 使用方法
./quick-fix-permissions.sh
```

**功能：**
- 修复 config/ 目录权限
- 修复 runtime/ 目录权限  
- 创建并修复 log/ 目录权限
- 修复 upload/ 目录权限
- 修复 php-temp/ 临时目录权限

### 2. `fix-permissions.sh` - 完整修复脚本
**完整版本，包含详细检查和错误处理**

```bash
# 使用方法
./fix-permissions.sh
```

**功能：**
- 服务器连接检查
- Docker容器状态检查
- 完整的目录权限修复
- 关键配置文件权限修复
- 权限修复结果验证
- 彩色输出和详细日志

## 常见使用场景

### 场景1：日常权限问题
```bash
# 快速修复，1条命令搞定
./quick-fix-permissions.sh
```

### 场景2：权限问题排查
```bash
# 使用完整版本，查看详细信息
./fix-permissions.sh
```

### 场景3：部署后权限设置
```bash
# 新部署后运行一次，确保权限正确
./fix-permissions.sh
```

## 脚本原理

**问题根因：**
- PHP-FPM 进程以 `www-data` 用户运行
- 文件属主是 `root`
- `www-data` 用户没有写权限

**解决方案：**
```bash
# 修改文件属主为 www-data
chown -R www-data:www-data /var/www/html/config/
chown -R www-data:www-data /var/www/html/runtime/
```

## 修复的目录/文件

| 路径 | 说明 | 权限 |
|------|------|------|
| `/var/www/html/config/` | 配置文件目录 | `www-data:www-data` |
| `/var/www/html/runtime/` | 运行时缓存 | `www-data:www-data` |
| `/var/www/html/log/` | 日志文件 | `www-data:www-data` |
| `/var/www/html/public/upload/` | 文件上传 | `www-data:www-data` |
| `/var/www/html/php-temp/` | PHP临时文件 | `www-data:www-data` |

## 服务器信息

- **应用服务器：** `38.180.189.204`
- **用户：** `root`
- **密码：** `ikJ234urdq`
- **容器名：** `9color_php73_prod`

## 注意事项

1. **网络要求：** 需要能够SSH连接到服务器
2. **依赖软件：** 需要安装 `sshpass`
3. **权限要求：** 需要有Docker容器的执行权限
4. **安全提醒：** 脚本包含明文密码，注意保护

## 安装依赖

### macOS
```bash
brew install hudochenkov/sshpass/sshpass
```

### Ubuntu/Debian
```bash
sudo apt-get install sshpass
```

### CentOS/RHEL
```bash
sudo yum install sshpass
```

## 常见错误处理

### 错误1：`sshpass: command not found`
**解决：** 安装sshpass依赖

### 错误2：`Permission denied (publickey,password)`
**解决：** 检查服务器密码是否正确

### 错误3：`No such container: 9color_php73_prod`
**解决：** 检查Docker容器是否正在运行

## 验证修复结果

修复完成后，可以通过以下命令验证：

```bash
# 登录服务器查看权限
ssh root@38.180.189.204
docker exec 9color_php73_prod ls -la /var/www/html/config/app.php

# 应该看到：
# -rw-r--r-- 1 www-data www-data 4761 config/app.php
```

## 更新记录

- **v1.0** - 初始版本，包含基础权限修复功能
- **v1.1** - 添加快速修复脚本，优化用户体验 