<?php
// 调试守护进程功能
echo "=== 调试守护进程功能 ===\n";
echo "当前时间: " . date('Y-m-d H:i:s') . "\n";

// 1. 测试基础PHP环境
echo "\n1. 测试PHP环境:\n";
echo "PHP版本: " . PHP_VERSION . "\n";
echo "内存限制: " . ini_get('memory_limit') . "\n";
echo "最大执行时间: " . ini_get('max_execution_time') . "\n";

// 2. 测试ThinkPHP加载
echo "\n2. 测试ThinkPHP加载:\n";
try {
    define('APP_PATH', '/var/www/html/application/');
    require_once '/var/www/html/thinkphp/base.php';
    
    // 加载数据库配置
    $dbConfig = include '/var/www/html/config/database.php';
    \think\Db::setConfig($dbConfig);
    
    echo "✓ ThinkPHP加载成功\n";
} catch (Exception $e) {
    echo "✗ ThinkPHP加载失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. 测试数据库连接
echo "\n3. 测试数据库连接:\n";
try {
    $count = \think\Db::name('xy_convey')->count();
    echo "✓ 数据库连接成功，订单总数: {$count}\n";
} catch (Exception $e) {
    echo "✗ 数据库连接失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 4. 测试模型加载
echo "\n4. 测试Convey模型:\n";
try {
    $conveyModel = new \app\admin\model\Convey();
    echo "✓ Convey模型加载成功\n";
} catch (Exception $e) {
    echo "✗ Convey模型加载失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 5. 测试查询待处理订单
echo "\n5. 查询待处理订单:\n";
try {
    $orders = \think\Db::name('xy_convey')
        ->where('auto_dispatch', 1)
        ->where('dispatch_status', 0)
        ->where('cooling_end_time', '>', 0)
        ->where('cooling_end_time', '<=', time())
        ->where('status', 0)
        ->select();
    
    echo "待处理订单数量: " . count($orders) . "\n";
    
    if (count($orders) > 0) {
        foreach ($orders as $order) {
            echo "- 订单ID: {$order['id']}, 冷却结束时间: " . date('Y-m-d H:i:s', $order['cooling_end_time']) . "\n";
        }
    }
} catch (Exception $e) {
    echo "✗ 查询订单失败: " . $e->getMessage() . "\n";
}

// 6. 测试processCoolingOrders方法
echo "\n6. 测试processCoolingOrders方法:\n";
try {
    $result = $conveyModel->processCoolingOrders();
    echo "✓ processCoolingOrders执行成功\n";
    echo "处理结果:\n";
    echo "- 发现订单: {$result['total_found']} 个\n";
    echo "- 处理成功: {$result['processed_count']} 个\n";
    
    if (!empty($result['errors'])) {
        echo "错误信息:\n";
        foreach ($result['errors'] as $error) {
            echo "- {$error}\n";
        }
    }
} catch (Exception $e) {
    echo "✗ processCoolingOrders执行失败: " . $e->getMessage() . "\n";
    echo "错误位置: " . $e->getFile() . " 第" . $e->getLine() . "行\n";
}

echo "\n=== 调试完成 ===\n";
?> 