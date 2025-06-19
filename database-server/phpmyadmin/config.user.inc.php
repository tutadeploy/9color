<?php
/**
 * phpMyAdmin configuration for HTTP access
 * This configuration allows phpMyAdmin to work properly over HTTP
 */

// Allow HTTP access and fix session cookie issues
$cfg['ForceSSL'] = false;
$cfg['SessionSavePath'] = '/tmp';

// Cookie settings for HTTP access
$cfg['blowfish_secret'] = 'H3!llo9c0l0rD@tab@se2024Secret!Key';

// Server configuration
$i = 0;

// First server - MySQL
$i++;
$cfg['Servers'][$i]['auth_type'] = 'cookie';
$cfg['Servers'][$i]['host'] = 'mysql';
$cfg['Servers'][$i]['port'] = 3306;
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;

// Fix for HTTP session issues
$cfg['CheckConfigurationPermissions'] = false;
$cfg['TempDir'] = '/tmp';

// Upload settings
$cfg['UploadDir'] = '/tmp';
$cfg['SaveDir'] = '/tmp';

// Memory and execution limits
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);

// Enhanced session configuration for HTTP
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_lifetime', 3600);
ini_set('session.gc_maxlifetime', 3600);

// Additional HTTP compatibility settings
$cfg['PmaAbsoluteUri'] = '';
$cfg['LoginCookieValidity'] = 3600;
$cfg['LoginCookieStore'] = 3600;
$cfg['LoginCookieDeleteAll'] = false;

// Disable problematic features for HTTP
$cfg['CaptchaLoginPublicKey'] = '';
$cfg['CaptchaLoginPrivateKey'] = '';

// Session name to avoid conflicts
ini_set('session.name', 'phpMyAdmin_9color');

// Additional cookie settings
$cfg['Servers'][$i]['auth_http_realm'] = 'phpMyAdmin';
$cfg['Servers'][$i]['auth_swekey_config'] = '';

// Error reporting
$cfg['Error_Handler']['display'] = true;
$cfg['Error_Handler']['gather'] = true; 