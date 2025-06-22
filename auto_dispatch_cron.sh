#!/bin/bash

# 9Color自动派单定时任务脚本
# 每分钟执行一次，替代数据库事件调度器

# 设置日志文件
LOG_FILE="/Users/josiahzhang/Repository/9color/logs/auto_dispatch.log"
mkdir -p "$(dirname "$LOG_FILE")"

# 记录开始时间
echo "[$(date '+%Y-%m-%d %H:%M:%S')] 开始执行自动派单检查..." >> "$LOG_FILE"

# 调用PHP自动派单接口
RESULT=$(curl -s "http://localhost/index/crontab/auto_dispatch" 2>&1)

# 记录结果
if [ $? -eq 0 ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] 执行成功: $RESULT" >> "$LOG_FILE"
else
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] 执行失败: $RESULT" >> "$LOG_FILE"
fi

# 保持日志文件大小，只保留最近1000行
tail -n 1000 "$LOG_FILE" > "$LOG_FILE.tmp" && mv "$LOG_FILE.tmp" "$LOG_FILE"
