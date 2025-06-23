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
                return $order['status'] == 0 ? '已派单，等待结算' : '已完成（手动）';
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
        
        // 检查用户余额是否为负数
        $userBalance = isset($order['ubalance']) ? floatval($order['ubalance']) : 0;
        $isNegativeBalance = $userBalance < 0;
        
        if ($order['status'] == 0) { // 待付款状态
            if (isset($order['auto_dispatch']) && $order['auto_dispatch'] == 1) {
                if ($order['dispatch_status'] == 0) {
                    if ($isNegativeBalance) {
                        // 用户余额为负数，手动结算按钮置灰
                        $actions[] = [
                            'type' => 'manual_settle_disabled', 
                            'text' => '手动结算', 
                            'class' => 'layui-btn-disabled',
                            'title' => '用户余额为负数，无法手动结算'
                        ];
                    } else {
                        $actions[] = ['type' => 'manual_settle', 'text' => '手动结算', 'class' => 'layui-btn-normal'];
                    }
                }
            } elseif (isset($order['manual_dispatch']) && $order['manual_dispatch'] == 1) {
                if ($order['dispatch_status'] == 0) {
                    // C状态：手动派单 + 无商品 = 显示匹配订单按钮（在派单模式列中）
                    // 操作列不显示任何按钮
                } elseif ($order['dispatch_status'] == 2) {
                    // D状态：手动派单 + 有商品 = 显示结算按钮
                    if ($isNegativeBalance) {
                        // 用户余额为负数，手动结算按钮置灰
                        $actions[] = [
                            'type' => 'manual_settle_disabled', 
                            'text' => '手动结算', 
                            'class' => 'layui-btn-disabled',
                            'title' => '用户余额为负数，无法手动结算'
                        ];
                    } else {
                        $actions[] = ['type' => 'manual_settle', 'text' => '手动结算', 'class' => 'layui-btn-normal'];
                    }
                }
            } else {
                // 兼容原有逻辑
                $actions[] = ['type' => 'force_pay', 'text' => '强制付款', 'class' => 'layui-btn'];
                $actions[] = ['type' => 'cancel', 'text' => '取消订单', 'class' => 'layui-btn-warm'];
            }
        } elseif ($order['status'] == 1) { // 已完成状态
            // 已完成的订单可以重新结算（如有需要）
            // $actions[] = ['type' => 'reprocess', 'text' => '重新处理', 'class' => 'layui-btn-primary'];
        } elseif (in_array($order['status'], [2, 3, 4, 5])) { // 取消、强制付款、系统取消、冻结状态
            // 这些状态的订单通常不需要额外操作，只保留删除功能
        }
        
        // 所有状态的订单都可以删除（管理员权限）
        $deleteText = '删除订单';
        $deleteConfirm = '确定删除此订单吗？';
        
        // 根据订单状态调整删除按钮的提示文字
        switch ($order['status']) {
            case 0:
                $deleteConfirm = '确定删除此订单吗？（待付款状态，无需退款）';
                break;
            case 1:
                $deleteConfirm = '确定删除此订单吗？将退回本金并扣除已发放的佣金！';
                break;
            case 2:
            case 4:
                $deleteConfirm = '确定删除此订单吗？（取消状态）';
                break;
            case 3:
                $deleteConfirm = '确定删除此订单吗？将退回强制付款金额！';
                break;
            case 5:
                $deleteConfirm = '确定删除此订单吗？将退回冻结金额！';
                break;
        }
        
        $actions[] = [
            'type' => 'delete', 
            'text' => $deleteText, 
            'class' => 'layui-btn-danger',
            'confirm' => $deleteConfirm
        ];
        
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
        
        // 已完成订单：显示灰色不可点击的标签
        if ($order['status'] == 1) {
            if (isset($order['auto_dispatch']) && $order['auto_dispatch'] == 1) {
                $html .= '<span class="layui-btn layui-btn-xs layui-btn-disabled">自动派单</span>';
            } elseif (isset($order['manual_dispatch']) && $order['manual_dispatch'] == 1) {
                $html .= '<span class="layui-btn layui-btn-xs layui-btn-disabled">手动派单</span>';
            } else {
                $html .= '<span class="layui-btn layui-btn-xs layui-btn-disabled">传统模式</span>';
            }
            return $html;
        }
        
        // 未完成订单：显示交互按钮
        if (isset($order['auto_dispatch']) && $order['auto_dispatch'] == 1) {
            // A状态或B状态：自动派单
            $html .= '<span class="layui-btn layui-btn-xs layui-btn-normal">自动派单</span>';
            $html .= '<a class="layui-btn layui-btn-xs layui-btn-primary" ';
            $html .= 'data-action="' . admin_url('admin/deal/toggle_order_dispatch') . '" ';
            $html .= 'data-value="id#' . $order['id'] . ';mode#manual">切换手动</a>';
        } elseif (isset($order['manual_dispatch']) && $order['manual_dispatch'] == 1) {
            // C状态或D状态：手动派单
            $html .= '<span class="layui-btn layui-btn-xs layui-btn-warm">手动派单</span>';
            $html .= '<a class="layui-btn layui-btn-xs layui-btn-primary" ';
            $html .= 'data-action="' . admin_url('admin/deal/toggle_order_dispatch') . '" ';
            $html .= 'data-value="id#' . $order['id'] . ';mode#auto">切换自动</a>';
            
            // C状态和D状态都显示匹配订单按钮
            // C状态：手动派单 + 无商品 = 匹配订单（初次分配商品）
            // D状态：手动派单 + 有商品 = 匹配订单（修改商品）
            if ($order['dispatch_status'] == 0 || $order['dispatch_status'] == 2) {
                $html .= '<a class="layui-btn layui-btn-xs layui-btn" ';
                $html .= 'data-open="' . admin_url('admin/deal/manual_dispatch') . '?order_id=' . $order['id'] . '">匹配订单</a>';
            }
        } else {
            // 传统模式
            $html .= '<span class="layui-btn layui-btn-xs">传统模式</span>';
            $html .= '<a class="layui-btn layui-btn-xs layui-btn-primary" ';
            $html .= 'data-action="' . admin_url('admin/deal/toggle_order_dispatch') . '" ';
            $html .= 'data-value="id#' . $order['id'] . ';mode#auto">启用自动</a>';
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
                    
                case 'manual_settle_disabled':
                    $title = isset($action['title']) ? $action['title'] : '无法手动结算';
                    $html .= '<span title="' . htmlspecialchars($title) . '" ';
                    $html .= 'class="layui-btn layui-btn-xs ' . $action['class'] . '">' . $action['text'] . '</span>';
                    break;
                    
                case 'match_order':
                    // 匹配订单按钮已经在派单模式列中显示，不在操作列重复显示
                    // $html .= '<a data-open="' . admin_url('admin/deal/manual_dispatch') . '?order_id=' . $order['id'] . '" ';
                    // $html .= 'class="layui-btn layui-btn-xs ' . $action['class'] . '">' . $action['text'] . '</a>';
                    break;
                    
                case 'delete':
                    $confirmText = isset($action['confirm']) ? $action['confirm'] : '确定删除此订单吗？余额将自动回退！';
                    $html .= '<a data-confirm="' . htmlspecialchars($confirmText) . '" ';
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