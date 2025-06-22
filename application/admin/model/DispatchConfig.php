<?php

namespace app\admin\model;

use think\Db;

/**
 * 派单配置管理类
 * 提供统一的配置读取和缓存机制
 */
class DispatchConfig
{
    /**
     * 配置缓存
     * @var array
     */
    private static $cache = [];

    /**
     * 获取配置值
     * @param string $key 配置键
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = Db::name('system_config')
                ->where('name', $key)
                ->value('value');
            
            if (self::$cache[$key] === null) {
                self::$cache[$key] = $default;
            }
        }
        
        return self::$cache[$key];
    }

    /**
     * 获取冷却期时长（秒）
     * @return int
     */
    public static function getCoolingPeriod()
    {
        return (int)self::get('cooling_period_minutes', 1) * 60;
    }

    /**
     * 检查是否启用自动派单
     * @return bool
     */
    public static function isAutoDispatchEnabled()
    {
        return self::get('auto_dispatch_enabled', 1) == 1;
    }

    /**
     * 设置配置值
     * @param string $key 配置键
     * @param mixed $value 配置值
     * @return bool
     */
    public static function set($key, $value)
    {
        $result = Db::name('system_config')
            ->where('name', $key)
            ->update(['value' => $value]);
        
        if ($result === 0) {
            // 如果更新失败，尝试插入
            $result = Db::name('system_config')
                ->insert(['name' => $key, 'value' => $value]);
        }
        
        // 更新缓存
        if ($result) {
            self::$cache[$key] = $value;
        }
        
        return $result !== false;
    }

    /**
     * 清除配置缓存
     * @param string|null $key 指定键，为null时清除所有缓存
     */
    public static function clearCache($key = null)
    {
        if ($key === null) {
            self::$cache = [];
        } else {
            unset(self::$cache[$key]);
        }
    }

    /**
     * 获取所有派单相关配置
     * @return array
     */
    public static function getAllDispatchConfig()
    {
        return [
            'auto_dispatch_enabled' => self::isAutoDispatchEnabled(),
            'cooling_period_minutes' => self::get('cooling_period_minutes', 1),
            'cooling_period_seconds' => self::getCoolingPeriod(),
        ];
    }
} 