<?php

namespace app\index\controller;

use think\Controller;
use think\Db;

/**
 * 后台守护进程控制器
 * 用于处理自动派单等后台任务
 */
class Daemon extends Controller
{
    /**
     * 自动派单守护进程（已停用 - 由数据库事件调度器替代）
     * Phase 5.4: 应用层适配
     */
    public function auto_dispatch()
    {
        echo "=== 守护进程已停用 ===\n";
        echo "自动派单现已由MySQL事件调度器处理\n";
        echo "每分钟自动检查并处理到期订单\n";
        echo "=====================================\n\n";
        
        echo "📊 查看处理日志:\n";
        echo "SELECT * FROM xy_auto_dispatch_log ORDER BY create_time DESC LIMIT 10;\n\n";
        
        echo "📈 查看监控数据:\n";
        echo "SELECT * FROM xy_dispatch_monitor ORDER BY check_time DESC LIMIT 10;\n\n";
        
        echo "🔧 手动触发检查:\n";
        echo "CALL ProcessAllExpiredOrders();\n\n";
        
        echo "⚡ 优势:\n";
        echo "- 数据库级原子性操作，彻底避免并发冲突\n";
        echo "- 自动恢复，无需人工维护\n";
        echo "- 高性能，避免应用层开销\n";
        echo "- 完善的日志和监控\n\n";
        
        echo "守护进程已被数据库级原子性处理方案替代！\n";
        return;
    }
    
    /**
     * 检查是否应该停止守护进程
     * @return bool
     */
    private function shouldStop()
    {
        $stopFile = APP_PATH . '../stop_daemon.flag';
        return file_exists($stopFile);
    }
    
    /**
     * 创建停止标记文件
     */
    public function stop()
    {
        $stopFile = APP_PATH . '../stop_daemon.flag';
        file_put_contents($stopFile, date('Y-m-d H:i:s'));
        echo "已创建停止标记文件: {$stopFile}\n";
        echo "守护进程将在下次检查时停止\n";
    }
    
    /**
     * 移除停止标记文件
     */
    public function start()
    {
        $stopFile = APP_PATH . '../stop_daemon.flag';
        if (file_exists($stopFile)) {
            unlink($stopFile);
            echo "已移除停止标记文件\n";
        }
        echo "可以启动守护进程了\n";
    }
    
    /**
     * 检查守护进程状态
     */
    public function status()
    {
        echo "=== 守护进程状态检查 ===\n";
        
        // 检查停止标记文件
        $stopFile = APP_PATH . '../stop_daemon.flag';
        if (file_exists($stopFile)) {
            $stopTime = file_get_contents($stopFile);
            echo "状态: 已停止\n";
            echo "停止时间: {$stopTime}\n";
        } else {
            echo "状态: 运行中（或未启动）\n";
        }
        
        // 检查是否有自动派单进程在运行（Docker环境）
        echo "\n=== 进程检查 ===\n";
        echo "请手动执行以下命令检查进程:\n";
        echo "docker exec 9color_php73_prod ps aux | grep auto_dispatch\n";
        
        // 检查最近的订单处理情况
        echo "\n=== 最近订单状态 ===\n";
        $pendingCount = Db::name('xy_convey')
            ->where('auto_dispatch', 1)
            ->where('dispatch_status', 0)
            ->where('cooling_end_time', '>', 0)
            ->where('cooling_end_time', '<=', time())
            ->where('status', 0)
            ->count();
            
        echo "待处理的到期订单数量: {$pendingCount}\n";
        
        if ($pendingCount > 0) {
            echo "建议立即启动守护进程处理这些订单\n";
        } else {
            echo "当前无待处理订单\n";
        }
    }
    
    /**
     * 手动触发一次自动派单检查（调用数据库存储过程）
     */
    public function test()
    {
        echo "=== 手动触发自动派单检查 ===\n";
        echo "执行时间: " . date('Y-m-d H:i:s') . "\n";
        echo "使用数据库存储过程处理\n";
        echo "=====================================\n";
        
        $startTime = microtime(true);
        
        try {
            // 调用数据库存储过程
            Db::execute('CALL ProcessAllExpiredOrders()');
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            
            // 获取最新的监控数据
            $monitor = Db::name('xy_dispatch_monitor')
                ->order('check_time DESC')
                ->find();
            
            if ($monitor) {
                echo "处理结果:\n";
                echo "- 发现订单: {$monitor['found_orders']} 个\n";
                echo "- 处理成功: {$monitor['processed_orders']} 个\n";
                echo "- 处理失败: {$monitor['failed_orders']} 个\n";
                echo "- 存储过程耗时: {$monitor['execution_time']}ms\n";
                echo "- 总耗时: {$executionTime}ms\n";
                
                if ($monitor['processed_orders'] > 0) {
                    echo "\n✓ 测试成功，数据库事件调度器功能正常\n";
                    
                    // 显示最近的处理日志
                    $logs = Db::name('xy_auto_dispatch_log')
                        ->where('create_time', '>=', date('Y-m-d H:i:s', time() - 300))
                        ->order('create_time DESC')
                        ->limit(5)
                        ->select();
                    
                    if ($logs) {
                        echo "\n📋 最近处理日志:\n";
                        foreach ($logs as $log) {
                            $status = $log['status'] == 'success' ? '✓' : '✗';
                            echo "{$status} {$log['order_id']} - {$log['status']} - {$log['create_time']}\n";
                        }
                    }
                } else if ($monitor['found_orders'] == 0) {
                    echo "\n✓ 测试成功，当前无待处理订单\n";
                } else {
                    echo "\n⚠ 有订单但未处理成功，请检查日志\n";
                    
                    // 显示错误日志
                    $errorLogs = Db::name('xy_auto_dispatch_log')
                        ->where('status', '!=', 'success')
                        ->where('create_time', '>=', date('Y-m-d H:i:s', time() - 300))
                        ->order('create_time DESC')
                        ->limit(3)
                        ->select();
                    
                    if ($errorLogs) {
                        echo "\n❌ 错误日志:\n";
                        foreach ($errorLogs as $log) {
                            echo "- {$log['order_id']}: {$log['error_msg']}\n";
                        }
                    }
                }
            } else {
                echo "⚠ 未找到监控数据，可能存储过程执行失败\n";
            }
            
        } catch (\Exception $e) {
            echo "✗ 测试失败: " . $e->getMessage() . "\n";
            echo "错误位置: " . $e->getFile() . " 第" . $e->getLine() . "行\n";
        }
    }
} 