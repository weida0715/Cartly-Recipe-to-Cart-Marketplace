<?php
declare(strict_types=1);

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Cartly');
}

if (!defined('BASE_URL')) {
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    define('BASE_URL', $scriptDir === '/' ? '' : $scriptDir);
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
