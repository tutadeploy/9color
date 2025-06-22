<?php
require_once 'thinkphp/base.php';

// 模拟订单数据
$order = [
    'id' => 'UB2506221622347844',
    'auto_dispatch' => 1,
    'dispatch_status' => 0,
    'status' => 0,
    'cooling_end_time' => 1750580614,
    'manual_dispatch' => 0
];

echo '订单数据:' . PHP_EOL;
print_r($order);

// 直接调用OrderStatusService
$actions = ppdmin\service\OrderStatusService::getAvailableActions($order);

echo PHP_EOL . '返回的按钮:' . PHP_EOL;
foreach ($actions as $action) {
    echo '- ' . $action['type'] . ': ' . $action['text'] . ' (' . $action['class'] . ')' . PHP_EOL;
}
?>
