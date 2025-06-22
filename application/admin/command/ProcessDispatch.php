<?php

namespace app\admin\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * 派单处理定时任务命令
 * 用法: php think dispatch:process
 */
class ProcessDispatch extends Command
{
    protected function configure()
    {
        $this->setName('dispatch:process')
            ->setDescription('处理自动派单定时任务');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln("开始执行自动派单处理...");
        
        try {
            // 1. 处理自动派单
            $this->processAutoDispatch($output);
            
            // 2. 清理过期数据（可选）
            $this->cleanupExpiredData($output);
            
            $output->writeln("自动派单处理完成！");
            return 0;
            
        } catch (\Exception $e) {
            $output->writeln("处理失败: " . $e->getMessage());
            $this->logError("自动派单处理异常", $e->getMessage());
            return 1;
        }
    }

    /**
     * 处理自动派单
     */
    private function processAutoDispatch(Output $output)
    {
        $conveyModel = model('admin/Convey');
        $result = $conveyModel->processCoolingOrders();
        
        $output->writeln("找到待处理订单: {$result['total_found']} 个");
        $output->writeln("成功处理订单: {$result['processed_count']} 个");
        
        if (!empty($result['errors'])) {
            $output->writeln("处理错误:");
            foreach ($result['errors'] as $error) {
                $output->writeln("  - {$error}");
            }
        }
        
        // 记录处理日志
        $this->logProcessResult($result);
    }

    /**
     * 清理过期数据
     */
    private function cleanupExpiredData(Output $output)
    {
        try {
            // 清理30天前的冷却期已结束但未处理的异常订单
            $expiredTime = time() - (30 * 24 * 60 * 60); // 30天前
            
            $count = Db::name('xy_convey')
                ->where('auto_dispatch', 1)
                ->where('dispatch_status', 0)
                ->where('cooling_end_time', '<', $expiredTime)
                ->where('cooling_end_time', '>', 0)
                ->count();
                
            if ($count > 0) {
                $output->writeln("发现 {$count} 个过期未处理的订单，建议人工检查");
                
                // 记录警告日志
                $this->logWarning("发现过期未处理订单", "数量: {$count}");
            }
            
        } catch (\Exception $e) {
            $output->writeln("清理过期数据时出错: " . $e->getMessage());
        }
    }

    /**
     * 记录处理结果
     */
    private function logProcessResult($result)
    {
        try {
            $logData = [
                'type' => 'auto_dispatch',
                'total_found' => $result['total_found'],
                'processed_count' => $result['processed_count'],
                'error_count' => count($result['errors']),
                'errors' => !empty($result['errors']) ? json_encode($result['errors'], JSON_UNESCAPED_UNICODE) : null,
                'execution_time' => time(),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // 如果有系统日志表，记录到系统日志
            if (Db::query("SHOW TABLES LIKE 'xy_system_log'")) {
                Db::name('xy_system_log')->insert([
                    'type' => 'auto_dispatch',
                    'content' => json_encode($logData, JSON_UNESCAPED_UNICODE),
                    'addtime' => time()
                ]);
            }
            
        } catch (\Exception $e) {
            error_log("定时任务日志记录失败: " . $e->getMessage());
        }
    }

    /**
     * 记录错误日志
     */
    private function logError($title, $message)
    {
        try {
            error_log("派单定时任务错误 - {$title}: {$message}");
            
            if (Db::query("SHOW TABLES LIKE 'xy_system_log'")) {
                Db::name('xy_system_log')->insert([
                    'type' => 'auto_dispatch_error',
                    'content' => json_encode(['title' => $title, 'message' => $message], JSON_UNESCAPED_UNICODE),
                    'addtime' => time()
                ]);
            }
        } catch (\Exception $e) {
            error_log("记录错误日志失败: " . $e->getMessage());
        }
    }

    /**
     * 记录警告日志
     */
    private function logWarning($title, $message)
    {
        try {
            error_log("派单定时任务警告 - {$title}: {$message}");
            
            if (Db::query("SHOW TABLES LIKE 'xy_system_log'")) {
                Db::name('xy_system_log')->insert([
                    'type' => 'auto_dispatch_warning',
                    'content' => json_encode(['title' => $title, 'message' => $message], JSON_UNESCAPED_UNICODE),
                    'addtime' => time()
                ]);
            }
        } catch (\Exception $e) {
            error_log("记录警告日志失败: " . $e->getMessage());
        }
    }
} 