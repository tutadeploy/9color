# 域名动态切换系统 - 快速开始指南

## 🚀 系统概述

本系统基于 **Cloudflare + ihosting + PHP + Nginx** 技术栈，实现三层域名动态切换架构，确保在域名被封锁或服务异常时能够快速切换到备用域名，保证服务的持续可用性。

### 核心特性

- ⚡ **响应速度快**: 客户端切换 < 1秒，DNS切换 < 5分钟
- 🛡️ **高可用性**: 99.999% 可用性保证  
- 💰 **成本可控**: 利用现有基础设施，增量成本低
- 🔧 **易于维护**: 自动化监控，简化运维
- 🌍 **全球优化**: 基于地理位置的智能路由

## 📋 系统要求

- **操作系统**: Linux (Ubuntu 18.04+ / CentOS 7+)
- **PHP**: 7.0 或更高版本
- **Web服务器**: Nginx
- **PHP扩展**: json, curl, mbstring
- **权限**: sudo/root 权限进行系统配置

## 🔧 一键安装

### 步骤1: 下载并运行部署脚本

```bash
# 进入项目根目录
cd /path/to/your/project

# 给脚本执行权限
chmod +x scripts/deploy-domain-switcher.sh

# 以root权限运行部署脚本
sudo ./scripts/deploy-domain-switcher.sh
```

### 步骤2: 配置域名

在部署过程中，脚本会提示您输入域名列表：

```
Domain: main.yourdomain.com
Domain: backup1.yourdomain.com  
Domain: backup2.yourdomain.com
Domain: backup3.yourdomain.com
Domain: [按回车结束]
```

### 步骤3: 集成到网页

在您的HTML模板中添加：

```html
<!-- 在 <head> 标签中添加 -->
<script src="/static/js/domain-switcher.js" defer></script>
```

## 📁 文件结构

部署完成后的文件结构：

```
项目根目录/
├── public/static/js/
│   └── domain-switcher.js          # 前端域名切换器
├── application/index/controller/
│   └── Health.php                  # 健康检查API控制器
├── route/
│   └── health.php                  # 健康检查路由配置
├── scripts/
│   ├── domain-monitor.php          # 域名监控脚本
│   ├── config.json                 # 监控配置文件
│   ├── logs/                       # 监控日志目录
│   └── reports/                    # 监控报告目录
└── runtime/log/
    └── domain_switches.log         # 域名切换日志
```

## 🔍 API接口

系统提供以下API接口：

### 健康检查接口
```bash
GET /api/health
```
返回系统健康状态信息

### 域名切换日志接口
```bash
POST /api/log-domain-switch
```
记录客户端域名切换事件

### 切换统计接口
```bash
GET /api/switch-stats
```
获取域名切换统计信息

## 🖥️ 监控管理

### 手动检查域名健康状态
```bash
php scripts/domain-monitor.php
```

### 生成健康报告
```bash
php scripts/domain-monitor.php --report
```

### 持续监控模式
```bash
php scripts/domain-monitor.php --continuous
```

### 查看定时任务
```bash
crontab -l
```

### 查看监控日志
```bash
tail -f scripts/logs/domain_monitor.log
```

## ⚙️ 配置文件

### JavaScript配置 (`public/static/js/domain-switcher.js`)

```javascript
this.domains = [
    window.location.hostname,    // 当前域名作为主域名
    'backup1.yourdomain.com',   // 备用域名1
    'backup2.yourdomain.com',   // 备用域名2
    'backup3.yourdomain.com'    // 备用域名3
];
```

### 监控配置 (`scripts/config.json`)

```json
{
    "domains": ["main.com", "backup1.com", "backup2.com"],
    "alert_webhook": "https://hooks.slack.com/...",
    "check_interval": 60,
    "alert_threshold": 0.5
}
```

## 🔔 告警设置

### Webhook告警

在 `scripts/config.json` 中配置webhook URL：

```json
{
    "alert_webhook": "https://hooks.slack.com/services/YOUR/WEBHOOK/URL"
}
```

### 邮件告警（可选）

可以扩展监控脚本支持邮件告警功能。

## 📊 监控数据

### 日志文件

- **域名监控日志**: `scripts/logs/domain_monitor.log`
- **切换事件日志**: `runtime/log/domain_switches.log`
- **告警历史**: `scripts/logs/alerts.log`
- **定时任务日志**: `scripts/logs/cron.log`

### 报告文件

- **每日报告**: `scripts/reports/daily-YYYY-MM-DD.json`
- **部署报告**: `scripts/deployment-report.txt`

## 🚨 故障排查

### 常见问题

**1. 健康检查API返回404**
```bash
# 检查路由配置
grep -r "api/health" route/
```

**2. 域名切换不工作**
```bash
# 检查JavaScript控制台错误
# 验证域名配置是否正确
```

**3. 监控脚本无法运行**
```bash
# 检查PHP路径和权限
which php
ls -la scripts/domain-monitor.php
```

### 调试模式

```bash
# 开启详细日志
php scripts/domain-monitor.php --report | jq '.'
```

## 🔐 安全注意事项

1. **API安全**: 健康检查API默认允许跨域访问，生产环境建议限制来源
2. **日志权限**: 确保日志文件只有web服务器用户可以写入
3. **配置保护**: `config.json` 不应包含敏感信息
4. **HTTPS**: 所有域名都应配置SSL证书

## 📈 性能优化

### 客户端优化
- 调整健康检查间隔 (`retryInterval`)
- 优化超时时间 (`healthCheckTimeout`)

### 服务端优化
- 启用HTTP/2
- 配置适当的缓存策略
- 使用CDN加速静态资源

## 🔄 更新升级

### 更新域名列表
```bash
# 编辑配置文件
vim scripts/config.json

# 重启监控服务（如果使用systemd）
systemctl restart domain-monitor.service
```

### 更新JavaScript
```bash
# 直接编辑文件
vim public/static/js/domain-switcher.js

# 清除浏览器缓存进行测试
```

## 📞 技术支持

如果遇到问题，请检查：

1. 部署报告: `scripts/deployment-report.txt`
2. 监控日志: `scripts/logs/domain_monitor.log`  
3. Web服务器错误日志
4. PHP错误日志

## 📝 维护清单

### 日常维护
- [ ] 检查监控日志是否正常
- [ ] 验证所有域名可访问性
- [ ] 查看切换统计数据

### 周期性维护
- [ ] 清理旧日志文件（自动）
- [ ] 更新域名证书
- [ ] 检查系统资源使用情况
- [ ] 测试故障切换功能

### 应急响应
- [ ] 准备应急域名
- [ ] 建立通知机制
- [ ] 制定切换方案
- [ ] 定期演练流程

---

**部署完成后，您将拥有一个强大且可靠的域名动态切换系统！** 🎉 