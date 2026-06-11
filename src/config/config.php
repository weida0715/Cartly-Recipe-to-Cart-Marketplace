<?php
declare(strict_types=1);

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Cartly');
}

// Auto-detect base URL (works under XAMPP at /cartly/src/public)
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    // define('BASE_URL', $scheme . '://' . $host . $scriptDir);
    define('BASE_URL', '');
}

if (!defined('ASSET_URL')) {
    define('ASSET_URL', BASE_URL . '/assets');
}

if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', dirname(__DIR__) . '/public/uploads');
}

if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', BASE_URL . '/uploads');
}

date_default_timezone_set('Asia/Kuala_Lumpur');
error_reporting(E_ALL);
ini_set('display_errors', '1');
