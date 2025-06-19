<?php
/**
 * 域名健康检查和切换相关路由配置
 * 支持跨域访问和OPTIONS预检请求
 */

use think\facade\Route;

// 健康检查路由 - 用于Cloudflare Load Balancing
Route::rule('api/health', 'index/Health/index', 'GET|HEAD|OPTIONS');
Route::rule('api/health/simple', 'index/Health/simple', 'GET|HEAD|OPTIONS');

// 域名切换日志记录 - 支持POST和OPTIONS  
Route::rule('api/log-domain-switch', 'index/Health/logDomainSwitch', 'POST|OPTIONS');

// 域名切换统计信息 - 支持GET和OPTIONS
Route::rule('api/switch-stats', 'index/Health/switchStats', 'GET|OPTIONS');

// API健康检查分组路由（可选，用于更复杂的路由管理）
Route::group('api', function () {
    // 详细健康检查
    Route::get('health/detailed', 'index/Health/index');
    
    // 快速健康检查（简化版）
    Route::get('health/quick', function () {
        return json([
            'status' => 'healthy',
            'timestamp' => time(),
            'server' => $_SERVER['HTTP_HOST'] ?? 'unknown'
        ]);
    });
    
    // 域名列表配置（用于前端动态获取域名列表）
    Route::get('domains', function () {
        // 这里可以从配置文件或数据库读取域名列表
        $domains = config('domains', []);
        
        return json([
            'status' => 'success',
            'domains' => $domains,
            'current' => $_SERVER['HTTP_HOST'] ?? '',
            'timestamp' => time()
        ]);
    });
    
})->middleware('cors'); // 如果有CORS中间件的话 