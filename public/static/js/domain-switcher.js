/**
 * 域名智能切换器
 * 实现客户端域名故障自动切换
 * 版本: 1.0.0
 */
class DomainSwitcher {
    constructor() {
        // 域名配置 - 请根据实际情况修改
        this.domains = [
            window.location.hostname,  // 当前域名作为主域名
            'backup1.example.com',     // 备用域名1 - 需要替换
            'backup2.example.com',     // 备用域名2 - 需要替换  
            'backup3.example.com'      // 备用域名3 - 需要替换
        ];
        
        this.currentDomainIndex = 0;
        this.healthCheckTimeout = 5000; // 5秒超时
        this.retryInterval = 30000;     // 30秒重试间隔
        this.maxRetries = 3;
        this.isChecking = false;
        
        this.init();
    }
    
    init() {
        console.log('Domain Switcher initialized with domains:', this.domains);
        
        // 从localStorage获取上次可用的域名
        const savedDomain = localStorage.getItem('activeDomain');
        if (savedDomain && this.domains.includes(savedDomain)) {
            this.currentDomainIndex = this.domains.indexOf(savedDomain);
            console.log('Restored saved domain:', savedDomain);
        }
        
        this.startHealthCheck();
        this.interceptAjaxFailures();
        
        // 添加页面可见性检测，页面变为可见时立即检查
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.performHealthCheck();
            }
        });
    }
    
    async checkDomainHealth(domain) {
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), this.healthCheckTimeout);
            
            const response = await fetch(`https://${domain}/api/health`, {
                method: 'GET',
                signal: controller.signal,
                cache: 'no-cache',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            clearTimeout(timeoutId);
            
            if (response.ok) {
                const data = await response.json();
                return data.status === 'healthy';
            }
            
            return false;
        } catch (error) {
            console.log(`Domain ${domain} health check failed:`, error.message);
            return false;
        }
    }
    
    async findBestDomain() {
        // 先检查当前域名
        const currentDomain = this.domains[this.currentDomainIndex];
        if (await this.checkDomainHealth(currentDomain)) {
            return currentDomain;
        }
        
        console.log(`Current domain ${currentDomain} is unhealthy, searching for alternatives...`);
        
        // 遍历其他域名寻找可用的
        for (let i = 0; i < this.domains.length; i++) {
            if (i === this.currentDomainIndex) continue;
            
            console.log(`Checking domain: ${this.domains[i]}`);
            if (await this.checkDomainHealth(this.domains[i])) {
                this.currentDomainIndex = i;
                localStorage.setItem('activeDomain', this.domains[i]);
                console.log(`Found healthy domain: ${this.domains[i]}`);
                return this.domains[i];
            }
        }
        
        console.warn('No healthy domains found!');
        return null;
    }
    
    async switchToNextDomain() {
        if (this.isChecking) {
            console.log('Switch already in progress, skipping...');
            return false;
        }
        
        this.isChecking = true;
        
        try {
            const bestDomain = await this.findBestDomain();
            
            if (bestDomain && bestDomain !== window.location.hostname) {
                console.log(`Switching from ${window.location.hostname} to ${bestDomain}`);
                
                // 记录切换日志
                this.logDomainSwitch(bestDomain);
                
                // 保存当前页面状态
                this.saveCurrentState();
                
                // 切换到新域名
                const newUrl = `https://${bestDomain}${window.location.pathname}${window.location.search}${window.location.hash}`;
                window.location.href = newUrl;
                return true;
            }
            
            return false;
        } finally {
            this.isChecking = false;
        }
    }
    
    async performHealthCheck() {
        if (this.isChecking) return;
        
        const currentDomain = window.location.hostname;
        const isHealthy = await this.checkDomainHealth(currentDomain);
        
        // 更新域名状态显示
        this.updateDomainStatus(isHealthy);
        
        if (!isHealthy) {
            console.log('Current domain unhealthy, attempting switch...');
            await this.switchToNextDomain();
        }
    }
    
    startHealthCheck() {
        // 立即执行一次检查
        this.performHealthCheck();
        
        // 定期健康检查
        setInterval(() => {
            this.performHealthCheck();
        }, this.retryInterval);
    }
    
    interceptAjaxFailures() {
        const originalFetch = window.fetch;
        const self = this;
        
        window.fetch = async function(...args) {
            let retryCount = 0;
            
            while (retryCount < self.maxRetries) {
                try {
                    const response = await originalFetch.apply(this, args);
                    if (response.ok) {
                        return response;
                    }
                    
                    // 如果是服务器错误，尝试切换域名
                    if (response.status >= 500) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    
                    return response;
                } catch (error) {
                    retryCount++;
                    console.log(`Fetch attempt ${retryCount} failed:`, error.message);
                    
                    if (retryCount >= self.maxRetries) {
                        // 最后一次尝试，切换域名
                        console.log('Max retries reached, attempting domain switch...');
                        const switched = await self.switchToNextDomain();
                        if (!switched) {
                            throw error;
                        }
                        return; // 页面将重定向
                    }
                    
                    // 等待一段时间后重试
                    await new Promise(resolve => setTimeout(resolve, 1000 * retryCount));
                }
            }
        };
        
        // 拦截XMLHttpRequest
        const originalXhrOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(method, url, ...args) {
            this.addEventListener('error', async () => {
                console.log('XMLHttpRequest failed, checking domain health...');
                await self.performHealthCheck();
            });
            
            return originalXhrOpen.call(this, method, url, ...args);
        };
    }
    
    saveCurrentState() {
        // 保存用户当前状态，便于切换后恢复
        const state = {
            timestamp: Date.now(),
            path: window.location.pathname,
            search: window.location.search,
            hash: window.location.hash,
            referrer: document.referrer,
            scrollY: window.scrollY
        };
        localStorage.setItem('pageState', JSON.stringify(state));
        localStorage.setItem('domainSwitchTime', Date.now().toString());
    }
    
    logDomainSwitch(newDomain) {
        // 记录域名切换日志
        const log = {
            timestamp: new Date().toISOString(),
            from: window.location.hostname,
            to: newDomain,
            userAgent: navigator.userAgent,
            url: window.location.href,
            referrer: document.referrer
        };
        
        // 发送切换日志到服务器
        this.sendSwitchLog(log);
        
        // 本地存储日志
        const localLogs = JSON.parse(localStorage.getItem('domainSwitchLogs') || '[]');
        localLogs.push(log);
        
        // 只保留最近50条记录
        if (localLogs.length > 50) {
            localLogs.splice(0, localLogs.length - 50);
        }
        
        localStorage.setItem('domainSwitchLogs', JSON.stringify(localLogs));
    }
    
    async sendSwitchLog(logData) {
        try {
            await fetch('/api/log-domain-switch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(logData)
            });
        } catch (error) {
            console.log('Failed to send switch log:', error);
        }
    }
    
    updateDomainStatus(isHealthy) {
        const statusEl = document.getElementById('domain-status');
        if (statusEl) {
            statusEl.textContent = isHealthy ? '域名正常' : '检测切换中...';
            statusEl.style.backgroundColor = isHealthy ? '#28a745' : '#dc3545';
            statusEl.style.display = 'block';
        }
    }
    
    // 获取切换统计信息
    getSwitchStats() {
        const logs = JSON.parse(localStorage.getItem('domainSwitchLogs') || '[]');
        const today = new Date().toDateString();
        const todayLogs = logs.filter(log => new Date(log.timestamp).toDateString() === today);
        
        return {
            totalSwitches: logs.length,
            todaySwitches: todayLogs.length,
            lastSwitch: logs.length > 0 ? logs[logs.length - 1] : null,
            currentDomain: window.location.hostname,
            availableDomains: this.domains.length
        };
    }
    
    // 手动触发域名检查
    async checkAllDomains() {
        const results = {};
        for (const domain of this.domains) {
            results[domain] = await this.checkDomainHealth(domain);
        }
        return results;
    }
}

// 全局初始化
window.domainSwitcher = new DomainSwitcher();

// 页面加载完成后额外检查
document.addEventListener('DOMContentLoaded', function() {
    // 恢复页面状态
    const savedState = localStorage.getItem('pageState');
    if (savedState) {
        try {
            const state = JSON.parse(savedState);
            // 如果是最近的切换(5分钟内)，可以执行一些恢复操作
            if (Date.now() - state.timestamp < 300000) {
                console.log('Restored from domain switch:', state);
                
                // 恢复滚动位置
                if (state.scrollY) {
                    window.scrollTo(0, state.scrollY);
                }
                
                // 显示切换提示
                const switchTime = localStorage.getItem('domainSwitchTime');
                if (switchTime && Date.now() - parseInt(switchTime) < 10000) {
                    console.log('Domain switch completed successfully');
                    // 可以在这里显示用户友好的提示
                }
            }
            localStorage.removeItem('pageState');
        } catch (error) {
            console.log('Failed to restore state:', error);
        }
    }
    
    // 创建域名状态显示元素
    if (!document.getElementById('domain-status')) {
        const statusDiv = document.createElement('div');
        statusDiv.id = 'domain-status';
        statusDiv.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 9999;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-family: Arial, sans-serif;
            cursor: pointer;
            display: none;
        `;
        statusDiv.textContent = '检测中...';
        
        // 点击显示详细信息
        statusDiv.addEventListener('click', () => {
            const stats = window.domainSwitcher.getSwitchStats();
            alert(`域名状态信息:\n当前域名: ${stats.currentDomain}\n今日切换: ${stats.todaySwitches}次\n总切换: ${stats.totalSwitches}次\n可用域名: ${stats.availableDomains}个`);
        });
        
        document.body.appendChild(statusDiv);
    }
});

// 导出给外部使用
window.DomainSwitcher = DomainSwitcher; 