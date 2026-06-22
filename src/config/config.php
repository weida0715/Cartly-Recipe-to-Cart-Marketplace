<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
cartly_load_env();

if (!defined('APP_NAME')) {
    define('APP_NAME', cartly_env('APP_NAME', 'Cartly'));
}

if (!defined('APP_ENV')) {
    define('APP_ENV', cartly_env('APP_ENV', 'development'));
}

if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', cartly_normalize_base_path(cartly_env('APP_BASE_PATH')));
}

if (!defined('BASE_URL')) {
    define('BASE_URL', cartly_app_base_url());
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
ini_set('display_errors', APP_ENV === 'production' ? '0' : '1');
