<?php

namespace app\admin\service;

/**
 * 订单状态管理服务类
 * 提供统一的状态管理和操作按钮生成
 */
class OrderStatusService
{
    /**
     * 获取订单状态文本
     * @param array $order 订单数据
     * @return string
     */
    public static function getStatusText($order)
    {
        // 优先判断派单状态
        if (isset($order['auto_dispatch']) && $order['auto_dispatch'] == 1) {
            if ($order['dispatch_status'] == 0) {
                $timeLeft = $order['cooling_end_time'] - time();
                if ($timeLeft > 0) {
                    return '冷却中 (' . ceil($timeLeft/60) . '分钟)';
                } else {
                    return '冷却结束，待自动结算';
                }
            } elseif ($order['dispatch_status'] == 1) {
                return '已完成（自动）';
            }
        } elseif (isset($order['manual_dispatch']) && $order['manual_dispatch'] == 1) {
            if ($order['dispatch_status'] == 0) {
                return '等待匹配订单';
            } elseif ($order['dispatch_status'] == 2) {
                return $order['status'] == 0 ? '等待手动结算' : '已完成（手动）';
            }
        }
        
        // 兼容原有状态
        return self::getLegacyStatusText($order['status']);
    }
    
    /**
     * 获取可用操作按钮
     * @param array $order 订单数据
     * @return array
     */
    public static function getAvailableActions($order)
    {
        $actions = [];
        
        if ($order['status'] == 0) { // 待付款状态
            if (isset($order['auto_dispatch']) && $order['auto_dispatch'] == 1) {
                if ($order['dispatch_status'] == 0) {
                    $actions[] = ['type' => 'manual_settle', 'text' => '手动结算', 'class' => 'layui-btn-normal'];
                }
            } elseif (isset($order['manual_dispatch']) && $order['manual_dispatch'] == 1) {
                if ($order['dispatch_status'] == 0) {
                    // 手动派单等待匹配状态：不在操作列添加匹配订单按钮，因为派单模式列中已经有了
                    // $actions[] = ['type' => 'match_order', 'text' => '匹配订单', 'class' => 'layui-btn'];
                } elseif ($order['dispatch_status'] == 2) {
                    $actions[] = ['type' => 'manual_settle', 'text' => '手动结算', 'class' => 'layui-btn-normal'];
                }
            } else {
                // 兼容原有逻辑
                $actions[] = ['type' => 'force_pay', 'text' => '强制付款', 'class' => 'layui-btn'];
                $actions[] = ['type' => 'cancel', 'text' => '取消订单', 'class' => 'layui-btn-warm'];
            }
            
            // 所有待付款订单都可以删除
            $actions[] = ['type' => 'delete', 'text' => '删除订单', 'class' => 'layui-btn-danger'];
        }
        
        return $actions;
    }
    
    /**
     * 检查是否可以执行某个操作
     * @param array $order 订单数据
     * @param string $action 操作类型
     * @return bool
     */
    public static function canPerformAction($order, $action)
    {
        $availableActions = self::getAvailableActions($order);
        return in_array($action, array_column($availableActions, 'type'));
    }
    
    /**
     * 获取传统状态文本（兼容原有逻辑）
     * @param int $status 状态值
     * @return string
     */
    private static function getLegacyStatusText($status)
    {
        switch ($status) {
            case 0: return '等待付款';
            case 1: return '完成付款';
            case 2: return '用户取消';
            case 3: return '强制付款';
            case 4: return '系统取消';
            case 5: return '订单冻结';
            default: return '未知状态';
        }
    }
    
    /**
     * 获取派单模式文本
     * @param array $order 订单数据
     * @return string
     */
    public static function getDispatchModeText($order)
    {
        if (isset($order['auto_dispatch']) && $order['auto_dispatch'] == 1) {
            return '自动派单';
        } elseif (isset($order['manual_dispatch']) && $order['manual_dispatch'] == 1) {
            return '手动派单';
        } else {
            return '传统模式';
        }
    }
    
    /**
     * 检查订单是否在冷却期
     * @param array $order 订单数据
     * @return bool
     */
    public static function isInCoolingPeriod($order)
    {
        return isset($order['auto_dispatch']) && 
               $order['auto_dispatch'] == 1 && 
               $order['dispatch_status'] == 0 && 
               $order['cooling_end_time'] > time();
    }
    
    /**
     * 获取派单模式按钮HTML
     * @param array $order 订单数据
     * @return string
     */
    public static function getDispatchModeButton($order)
    {
        $html = '';
        
        if (isset($order['auto_dispatch']) && $order['auto_dispatch'] == 1) {
            $html .= '<span class="layui-btn layui-btn-xs layui-btn-normal">自动派单</span>';
            $html .= '<a class="layui-btn layui-btn-xs layui-btn-primary" ';
            $html .= 'data-action="' . admin_url('admin/deal/toggle_order_dispatch') . '" ';
            $html .= 'data-value="id#' . $order['id'] . ';mode#manual">切换手动</a>';
        } elseif (isset($order['manual_dispatch']) && $order['manual_dispatch'] == 1) {
            $html .= '<span class="layui-btn layui-btn-xs layui-btn-warm">手动派单</span>';
            $html .= '<a class="layui-btn layui-btn-xs layui-btn-primary" ';
            $html .= 'data-action="' . admin_url('admin/deal/toggle_order_dispatch') . '" ';
            $html .= 'data-value="id#' . $order['id'] . ';mode#auto">切换自动</a>';
        } else {
            $html .= '<span class="layui-btn layui-btn-xs">传统模式</span>';
        }
        
        return $html;
    }
    
    /**
     * 获取操作按钮HTML
     * @param array $order 订单数据
     * @return string
     */
    public static function getActionButtonsHtml($order)
    {
        $actions = self::getAvailableActions($order);
        $html = '';
        
        foreach ($actions as $action) {
            switch ($action['type']) {
                case 'manual_settle':
                    $html .= '<a data-confirm="确定手动结算此订单吗？" ';
                    $html .= 'data-action="' . admin_url('admin/deal/manual_settlement') . '" ';
                    $html .= 'data-value="id#' . $order['id'] . '" ';
                    $html .= 'class="layui-btn layui-btn-xs ' . $action['class'] . '">' . $action['text'] . '</a>';
                    break;
                    
                case 'match_order':
                    // 匹配订单按钮已经在派单模式列中显示，不在操作列重复显示
                    // $html .= '<a data-open="' . admin_url('admin/deal/manual_dispatch') . '?order_id=' . $order['id'] . '" ';
                    // $html .= 'class="layui-btn layui-btn-xs ' . $action['class'] . '">' . $action['text'] . '</a>';
                    break;
                    
                case 'delete':
                    $html .= '<a data-confirm="确定删除此订单吗？余额将自动回退！" ';
                    $html .= 'data-action="' . admin_url('admin/deal/delete_order_with_refund') . '" ';
                    $html .= 'data-value="id#' . $order['id'] . '" ';
                    $html .= 'class="layui-btn layui-btn-xs ' . $action['class'] . '">' . $action['text'] . '</a>';
                    break;
                    
                case 'force_pay':
                    $html .= '<a data-action="' . admin_url('admin/deal/do_user_order') . '" ';
                    $html .= 'data-value="id#' . $order['id'] . ';status#3" ';
                    $html .= 'class="layui-btn layui-btn-xs ' . $action['class'] . '">' . $action['text'] . '</a>';
                    break;
                    
                case 'cancel':
                    $html .= '<a data-action="' . admin_url('admin/deal/do_user_order') . '" ';
                    $html .= 'data-value="id#' . $order['id'] . ';status#4" ';
                    $html .= 'class="layui-btn layui-btn-xs ' . $action['class'] . '">' . $action['text'] . '</a>';
                    break;
            }
        }
        
        return $html;
    }
    
    /**
     * 获取状态颜色类
     * @param array $order 订单数据
     * @return string
     */
    public static function getStatusColorClass($order)
    {
        if (isset($order['auto_dispatch']) && $order['auto_dispatch'] == 1) {
            if ($order['dispatch_status'] == 0) {
                return self::isInCoolingPeriod($order) ? 'text-warning' : 'text-info';
            } elseif ($order['dispatch_status'] == 1) {
                return 'text-success';
            }
        } elseif (isset($order['manual_dispatch']) && $order['manual_dispatch'] == 1) {
            if ($order['dispatch_status'] == 0) {
                return 'text-primary';
            } elseif ($order['dispatch_status'] == 2) {
                return $order['status'] == 0 ? 'text-warning' : 'text-success';
            }
        }
        
        // 传统状态颜色
        switch ($order['status']) {
            case 0: return 'text-warning';
            case 1: return 'text-success';
            case 2:
            case 4: return 'text-muted';
            case 3: return 'text-info';
            case 5: return 'text-danger';
            default: return '';
        }
    }
} 