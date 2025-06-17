# 9Color 本地 Nginx + PHP-FPM 环境

## 启动命令

```bash
cd local-nginx-fpm
docker-compose up -d
```

## 访问地址

- 前端: http://localhost:8080
- 管理后台: http://localhost:8080/admin
- MySQL: localhost:3307

## 停止服务

```bash
docker-compose down
```

## 重新构建

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## 查看日志

```bash
# 查看所有服务日志
docker-compose logs -f

# 查看特定服务日志
docker-compose logs -f nginx
docker-compose logs -f php-fpm
docker-compose logs -f mysql
```
