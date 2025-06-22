#!/bin/bash

# 9Color时间窗口管理脚本
# 为管理操作提供安全的时间窗口，避免与自动派单冲突

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# 获取当前时间信息
get_time_info() {
    CURRENT_HOUR=$(date '+%H')
    CURRENT_MINUTE=$(date '+%M')
    CURRENT_MINUTE_NUM=$((10#$CURRENT_MINUTE))
    CURRENT_TIME=$(date '+%H:%M')
}

# 检查当前是否为安全操作时间
check_safe_window() {
    get_time_info

    if [ $CURRENT_MINUTE_NUM -ge 51 ] && [ $CURRENT_MINUTE_NUM -le 59 ]; then
        return 0 # 安全时间窗口
    else
        return 1 # 非安全时间窗口
    fi
}

# 计算下一个安全时间窗口
get_next_safe_window() {
    get_time_info

    if [ $CURRENT_MINUTE_NUM -lt 51 ]; then
        # 当前小时内的安全窗口
        echo "${CURRENT_HOUR}:51-${CURRENT_HOUR}:59"
    else
        # 下一小时的安全窗口
        local next_hour=$(((10#$CURRENT_HOUR + 1) % 24))
        printf "%02d:51-%02d:59" $next_hour $next_hour
    fi
}

# 等待安全时间窗口
wait_for_safe_window() {
    local max_wait=${1:-300} # 默认最大等待5分钟
    local waited=0

    while ! check_safe_window && [ $waited -lt $max_wait ]; do
        get_time_info
        local next_window=$(get_next_safe_window)
        print_info "当前时间 $CURRENT_TIME，等待安全操作窗口: $next_window"
        sleep 10
        waited=$((waited + 10))
    done

    if check_safe_window; then
        print_success "进入安全操作时间窗口"
        return 0
    else
        print_warning "等待超时，当前可能不是最佳操作时间"
        return 1
    fi
}

# 显示时间窗口状态
show_window_status() {
    get_time_info

    echo "======================================="
    echo "        时间窗口管理状态"
    echo "======================================="
    print_info "当前时间: $CURRENT_TIME"

    if check_safe_window; then
        print_success "当前为安全管理操作时间 (${CURRENT_HOUR}:51-${CURRENT_HOUR}:59)"
        print_info "可以安全执行: 删除订单、修改配置、数据维护等"
    else
        print_warning "当前为自动派单活跃时间"
        local next_window=$(get_next_safe_window)
        print_info "下一个安全操作窗口: $next_window"
        print_info "建议等待安全时间窗口再进行管理操作"
    fi

    echo ""
    print_info "时间窗口说明:"
    echo "  🤖 00-50分: 自动派单活跃时间"
    echo "  👨‍💼 51-59分: 管理操作安全时间"
    echo "======================================="
}

# 强制进入管理模式（紧急情况使用）
force_admin_mode() {
    print_warning "强制进入管理模式 - 仅用于紧急情况"
    print_info "建议操作完成后尽快退出管理模式"

    # 这里可以添加额外的安全措施
    # 比如临时调整定时任务频率等
}

# 显示帮助信息
show_help() {
    echo "时间窗口管理脚本使用说明:"
    echo ""
    echo "命令:"
    echo "  status          显示当前时间窗口状态"
    echo "  wait [秒数]     等待安全操作时间窗口"
    echo "  check           检查当前是否为安全时间"
    echo "  next            显示下一个安全时间窗口"
    echo "  force           强制进入管理模式(紧急使用)"
    echo "  help            显示此帮助信息"
    echo ""
    echo "时间窗口规则:"
    echo "  每小时 00-50分: 自动派单活跃时间"
    echo "  每小时 51-59分: 管理操作安全时间"
}

# 主函数
main() {
    case "${1:-status}" in
    "status")
        show_window_status
        ;;
    "wait")
        wait_for_safe_window "${2:-300}"
        ;;
    "check")
        if check_safe_window; then
            print_success "当前为安全操作时间"
            exit 0
        else
            print_warning "当前非安全操作时间"
            exit 1
        fi
        ;;
    "next")
        local next_window=$(get_next_safe_window)
        print_info "下一个安全操作窗口: $next_window"
        ;;
    "force")
        force_admin_mode
        ;;
    "help" | "-h" | "--help")
        show_help
        ;;
    *)
        print_error "未知命令: $1"
        show_help
        exit 1
        ;;
    esac
}

# 执行主函数
main "$@"
