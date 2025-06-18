<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2019 
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 

// +----------------------------------------------------------------------

// 获取请求的URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 如果请求的是 .html 结尾的URL，去掉 .html 后缀
if (preg_match('/\.html$/', $uri)) {
    $uri = preg_replace('/\.html$/', '', $uri);
}

// 处理根路径和特殊路径
if ($uri === '/') {
    // 根路径重定向到首页
    $_SERVER['REQUEST_URI'] = '/index/index/index';
} else if ($uri === '/login') {
    // 登录页面路由 - 设置正确的ThinkPHP参数
    $_GET['s'] = '/index/user/login';
    $_SERVER['REQUEST_URI'] = '/index.php?s=/index/user/login';
} else if ($uri === '/register') {
    // 注册页面路由
    $_GET['s'] = '/index/user/register';
    $_SERVER['REQUEST_URI'] = '/index.php?s=/index/user/register';
} else if ($uri === '/sgcpj' || preg_match('/^\/sgcpj\//', $uri)) {
    // 后台管理路由 - 直接路由到admin模块
    if ($uri === '/sgcpj') {
        $_GET['s'] = '/admin/index/index';
        $_SERVER['REQUEST_URI'] = '/index.php?s=/admin/index/index';
    } else {
        // 处理 /sgcpj/controller/action 格式
        $adminPath = preg_replace('/^\/sgcpj/', '/admin', $uri);
        $_GET['s'] = $adminPath;
        $_SERVER['REQUEST_URI'] = '/index.php?s=' . $adminPath;
    }
} else if (preg_match('/^\/admin\/sgcpj\//', $uri)) {
    // 容错处理：修复错误的 /admin/sgcpj/ URL
    $adminPath = preg_replace('/^\/admin\/sgcpj/', '/admin', $uri);
    $_GET['s'] = $adminPath;
    $_SERVER['REQUEST_URI'] = '/index.php?s=' . $adminPath;
} else if (preg_match('/^\/index\/user\/(do_login|do_register|logout)$/', $uri, $matches)) {
    // 处理用户相关的API接口
    $_GET['s'] = $uri;
    $_SERVER['REQUEST_URI'] = '/index.php?s=' . $uri;
} else if (preg_match('/^\/index\//', $uri)) {
    // 处理其他ThinkPHP标准路由格式
    $_GET['s'] = $uri;
    $_SERVER['REQUEST_URI'] = '/index.php?s=' . $uri;
} else {
    // 处理前台单级路径，映射到对应的控制器
    // 例如: /order -> /index/order/index, /rotOrder -> /index/rot_order/index
    $controllerMap = [
        '/order' => '/index/order/index',
        '/rotOrder' => '/index/rot_order/index',
        '/my' => '/index/my/index',
        '/support' => '/index/support/index',
        '/shop' => '/index/shop/index',
        '/ctrl' => '/index/ctrl/index',
        '/api' => '/index/api/index',
    ];
    
    // 检查是否是映射的控制器路径
    if (isset($controllerMap[$uri])) {
        $_GET['s'] = $controllerMap[$uri];
        $_SERVER['REQUEST_URI'] = '/index.php?s=' . $controllerMap[$uri];
    } else if (preg_match('/^\/(\w+)$/', $uri, $matches)) {
        // 对于其他单级路径，尝试映射到 /index/控制器/index
        $controller = $matches[1];
        $_GET['s'] = '/index/' . $controller . '/index';
        $_SERVER['REQUEST_URI'] = '/index.php?s=/index/' . $controller . '/index';
    } else if (preg_match('/^\/(\w+)\/(.+)$/', $uri, $matches)) {
        // 对于多级路径，映射到 /index/控制器/方法
        $controller = $matches[1];
        $action = $matches[2];
        $_GET['s'] = '/index/' . $controller . '/' . $action;
        $_SERVER['REQUEST_URI'] = '/index.php?s=/index/' . $controller . '/' . $action;
    } else {
        // 其他情况，保持原URI
        $_SERVER['REQUEST_URI'] = $uri;
    }
}

// 直接转发到 index.php
require __DIR__ . '/index.php';
