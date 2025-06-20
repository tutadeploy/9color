<?php
/**
 * phpMyAdmin configuration for HTTP access
 * 简化配置，主要通过环境变量控制
 */

// 基本设置
$cfg['ForceSSL'] = false;
$cfg['blowfish_secret'] = 'H3!llo9c0l0rD@tab@se2024Secret!Key';

// 服务器配置
$i = 1;
$cfg['Servers'][$i]['auth_type'] = 'cookie';
$cfg['Servers'][$i]['host'] = 'mysql';
$cfg['Servers'][$i]['port'] = 3306;
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;

// HTTP兼容设置
$cfg['CheckConfigurationPermissions'] = false;
$cfg['LoginCookieValidity'] = 3600;

// 错误显示
$cfg['Error_Handler']['display'] = true; 