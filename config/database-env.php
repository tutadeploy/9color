<?php

// 9Color 数据库配置 - 支持环境变量
return [
    // 数据库调试模式
    'debug'       => env('DB_DEBUG', false),
    
    // 数据库类型
    'type'        => env('DB_TYPE', 'mysql'),
    
    // 服务器地址 - 支持环境变量
    'hostname'    => env('DB_HOST', '9color_mysql_prod'),
    
    // 数据库名
    'database'    => env('DB_NAME', '6ui'),
    
    // 用户名
    'username'    => env('DB_USER', 'app'),
    
    // 密码
    'password'    => env('DB_PASS', 'app123456'),
    
    // 编码
    'charset'     => 'utf8mb4',
    
    // 端口
    'hostport'    => env('DB_PORT', '3306'),
    
    // 连接参数
    'params'      => [
        // 设置连接超时
        \PDO::ATTR_TIMEOUT => 30,
        // 持久连接
        \PDO::ATTR_PERSISTENT => false,
        // 设置字符集
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        // 错误模式
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ],
    
    // 主从配置
    'deploy'      => env('DB_DEPLOY', 0),
    
    // 读写分离
    'rw_separate' => env('DB_RW_SEPARATE', false),
    
    // 主服务器（写）
    'master'      => [
        'hostname' => env('DB_MASTER_HOST', env('DB_HOST', '9color_mysql_prod')),
        'hostport' => env('DB_MASTER_PORT', env('DB_PORT', '3306')),
        'username' => env('DB_MASTER_USER', env('DB_USER', 'app')),
        'password' => env('DB_MASTER_PASS', env('DB_PASS', 'app123456')),
        'database' => env('DB_MASTER_NAME', env('DB_NAME', '6ui')),
    ],
    
    // 从服务器（读）
    'slave'       => [
        [
            'hostname' => env('DB_SLAVE_HOST', env('DB_HOST', '9color_mysql_prod')),
            'hostport' => env('DB_SLAVE_PORT', env('DB_PORT', '3306')),
            'username' => env('DB_SLAVE_USER', 'readonly'),
            'password' => env('DB_SLAVE_PASS', 'readonly123456'),
            'database' => env('DB_SLAVE_NAME', env('DB_NAME', '6ui')),
        ],
    ],
    
    // 连接池配置
    'pool'        => [
        'max_connections' => env('DB_POOL_MAX', 20),
        'min_connections' => env('DB_POOL_MIN', 5),
    ],
    
    // 查询缓存配置
    'cache'       => [
        'type'   => env('CACHE_TYPE', 'redis'),
        'host'   => env('REDIS_HOST', '127.0.0.1'),
        'port'   => env('REDIS_PORT', 6379),
        'expire' => env('CACHE_EXPIRE', 3600),
    ],
];

/**
 * 环境变量获取函数
 */
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        
        // 类型转换
        switch (strtolower($value)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return null;
            default:
                return $value;
        }
    }
} 