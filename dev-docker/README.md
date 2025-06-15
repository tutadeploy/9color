# 🐳 Docker 开发环境

> **注意**：此目录包含个人开发环境配置，已在`.gitignore`中忽略，不会提交到版本库。

## 🚀 快速启动

```bash
# 进入项目根目录，然后启动开发环境
cd /path/to/9color
docker-compose -f dev-docker/docker-compose.dev.yml up --build

# 后台运行
docker-compose -f dev-docker/docker-compose.dev.yml up -d --build
```

## 🌐 访问地址

- **主应用**: http://localhost (Apache)
- **PHP 内置服务器**: http://localhost:8080 (推荐，使用 custom router)
- **备选端口**: http://localhost:8000 (可选开发端口)
- **数据库管理**: http://localhost:8081 (PHPMyAdmin)

### 🌍 局域网访问

- **本机 IP**: http://192.168.50.197:8080 (支持其他设备访问)
- **手机/平板**: 连接同一 WiFi 后访问上述地址

### 🗄️ 数据库配置

- **统一数据库**: localhost:3306 (docker-deploy/生产级配置)
- **数据库名**: 6ui
- **认证信息**: root/root123456

## 🛠 开发特性

- ✅ **PHP 7.3.33** 环境
- ✅ **热重载** - 代码修改实时生效
- ✅ **Xdebug 调试** - 端口 9000
- ✅ **MySQL 8.0** 数据库
- ✅ **ThinkPHP** URL 重写支持

## 🔧 IDE 调试配置

### PHPStorm 配置

1. 设置 PHP 解释器：`Docker` -> `从容器中选择`
2. 配置 Xdebug：
   - Host: `host.docker.internal`
   - Port: `9000`
   - IDE Key: `PHPSTORM`

### VS Code 配置

```json
{
  "name": "Listen for Xdebug",
  "type": "php",
  "request": "launch",
  "port": 9000,
  "pathMappings": {
    "/var/www/html": "${workspaceFolder}"
  }
}
```

## 📝 常用命令

```bash
# 查看日志
docker-compose -f dev-docker/docker-compose.dev.yml logs -f php-dev

# 进入PHP容器
docker-compose -f dev-docker/docker-compose.dev.yml exec php-dev bash

# 停止服务
docker-compose -f dev-docker/docker-compose.dev.yml down

# 重建容器
docker-compose -f dev-docker/docker-compose.dev.yml up --build --force-recreate

# 启用8000端口（可选）
ENABLE_PORT_8000=true docker-compose -f dev-docker/docker-compose.dev.yml up -d
```

## 🔧 开发命令对照

**传统启动方式**：

```bash
php -S 0.0.0.0:8000 -t public public/router.php
```

**Docker 方式**：

```bash
docker-compose -f dev-docker/docker-compose.dev.yml up -d
# 访问 http://localhost:8080 或 http://192.168.50.197:8080
```

## 💡 个性化配置

你可以根据需要修改以下文件：

- `docker-compose.dev.yml` - 服务配置和端口
- `Dockerfile.dev` - PHP 扩展和环境
- `docker-config/php-dev.ini` - PHP 配置
- `docker-config/apache-dev.conf` - Apache 配置

## 🚀 新特性：智能 URL 重写 ✅ 真正完全实现

### 问题解决

- **问题**：ThinkPHP 多模块模式要求 URL 格式为 `/模块/控制器/方法`
- **原来**：必须访问 `http://localhost:8080/index/my/invite`
- **现在**：可以直接访问 `http://localhost:8080/my/invite` ✅

### ⚠️ 修复历程

1. **第一版问题**：只覆盖了部分控制器（my, user, shop, api）
2. **第二版问题**：虽然添加了所有控制器，但遗漏了单独路径处理
3. **最终版本**：现在完全支持两种 URL 模式：
   - 单独控制器：`/order` → `/index/order/index`
   - 带方法路径：`/order/xxx` → `/index/order/xxx`

### URL 重写规则 - 完整支持

**单独控制器路径**（自动添加默认 index 方法）：

```
/my        → /index/my/index        ✅
/user      → /index/user/index      ✅
/shop      → /index/shop/index      ✅
/api       → /index/api/index       ✅
/order     → /index/order/index     ✅ 修复完成
/ctrl      → /index/ctrl/index      ✅
/pay       → /index/pay/index       ✅
/rotorder  → /index/rot_order/index  ✅ (修复路径映射)
/rot_order → /index/rot_order/index  ✅
/send      → /index/send/index      ✅
/support   → /index/support/index   ✅
/crontab   → /index/crontab/index   ✅
```

**带方法的完整路径**：

```
/my/*       → /index/my/*       ✅
/user/*     → /index/user/*     ✅
/shop/*     → /index/shop/*     ✅
/api/*      → /index/api/*      ✅
/order/*    → /index/order/*    ✅
/ctrl/*     → /index/ctrl/*     ✅
/pay/*      → /index/pay/*      ✅
/rotorder/* → /index/rot_order/* ✅ (修复路径映射)
/rot_order/* → /index/rot_order/* ✅
/send/*     → /index/send/*     ✅
/support/*  → /index/support/*  ✅
/crontab/*  → /index/crontab/*  ✅
```

### 最终验证结果

✅ **单独控制器路径测试通过**：

```bash
# 测试单独路径 - 问题已完全解决！
curl -I http://localhost:8080/order
# 返回：Location: http://localhost:8080/index/order/index

curl -I http://localhost:8080/my
# 返回：Location: http://localhost:8080/index/my/index

curl -I http://localhost:8080/support
# 返回：Location: http://localhost:8080/index/support/index
```

✅ **带方法路径测试通过**：

```bash
curl -I http://localhost:8080/order/list
# 返回：Location: http://localhost:8080/index/order/list

curl -I http://localhost:8080/my/invite
# 返回：Location: http://localhost:8080/index/my/invite
```

✅ **代理工作正常**：nginx 正确代理请求到 PHP 容器

✅ **端口映射正确**：重定向 URL 包含正确的端口号

## 架构说明

```
请求流向：
浏览器 → Nginx代理(:8080) → PHP+Apache容器(:80) → ThinkPHP应用

URL重写过程：
/my/invite → /index/my/invite → My控制器invite方法
```

### 端口分配

- **8080**: Nginx 代理对外端口（主要访问入口）
- **8081**: PHP 内置服务器（内部端口，调试用）
- **8082**: Apache 服务器（内部端口，nginx 代理目标）
- **8000**: 额外的开发端口

## 开发调试

### Xdebug 配置

IDE 中配置服务器名称：`9color`

### 日志查看

```bash
# Nginx访问日志
docker-compose -f docker-compose.dev.yml logs nginx-proxy

# PHP应用日志
docker-compose -f docker-compose.dev.yml logs php-dev
```

### 配置文件位置

- `docker-config/nginx.conf` - Nginx 主配置
- `docker-config/nginx-dev.conf` - 虚拟主机配置（URL 重写规则）
- `docker-config/apache-dev.conf` - Apache 配置
- `docker-config/php-dev.ini` - PHP 配置

## 停止环境

```bash
docker-compose -f docker-compose.dev.yml down
```

## 故障排除

### 1. URL 重写不生效

```bash
# 检查nginx配置语法
docker exec 9color-nginx-proxy nginx -t

# 重载nginx配置
docker exec 9color-nginx-proxy nginx -s reload
```

### 2. 容器无法启动

```bash
# 查看详细日志
docker-compose -f docker-compose.dev.yml logs --tail=50
```

### 3. 端口冲突

如果 8080 端口被占用，修改`docker-compose.dev.yml`中的端口映射：

```yaml
nginx-proxy:
  ports:
    - "8090:80" # 改为8090端口
```

## 技术实现

### URL 重写原理

```nginx
# 关键配置片段
location ~ ^/my/(.*)$ {
    proxy_pass http://php-dev:80/index/my/$1$is_args$args;
    proxy_set_header Host $http_host;
    # ... 其他配置
}
```

### 重写策略

1. **nginx location 匹配**：使用正则表达式匹配特定路径
2. **proxy_pass 重写**：直接在代理时重写目标 URL
3. **参数传递**：使用`$is_args$args`确保查询参数正确传递
4. **Host 头处理**：使用`$http_host`保持原始 Host 头

## 总结

✅ **URL 重写功能已成功实现**！

现在你可以：

- 使用简化的 URL：`http://localhost:8080/my/invite`
- 原有 URL 仍然有效：`http://localhost:8080/index/my/invite`
- 支持所有控制器：`/my/*`, `/user/*`, `/shop/*`, `/api/*`

这样解决了 ThinkPHP 多模块 URL 解析的问题，让 API 调用更加简洁！

### 注意事项

- 访问需要登录的页面（如`/my/invite`）会自动重定向到登录页面
- 这是正常的应用行为，不是 nginx 配置问题
- 可以通过浏览器访问完整流程：登录后访问相应页面
