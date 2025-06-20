; PHP初始化配置文件 - 在phpMyAdmin启动前设置session配置
; 这个文件会在Apache启动时自动加载

; Session配置
session.name = "phpMyAdmin_9color"
session.cookie_secure = 0
session.cookie_httponly = 1
session.cookie_samesite = "Lax"
session.use_only_cookies = 1
session.cookie_lifetime = 0
session.gc_maxlifetime = 3600
session.use_strict_mode = 1

; 错误报告
display_errors = Off
log_errors = On

; 内存和执行时间限制
memory_limit = 512M
max_execution_time = 300
max_input_time = 300
post_max_size = 100M
upload_max_filesize = 100M 