<?php
// Front controller for Cartly (run under XAMPP: http://localhost/cartly/src/public/)

declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require_once APP_PATH . '/helper/Autoload.php';
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/database.php';
require_once APP_PATH . '/helper/Router.php';
require_once APP_PATH . '/helper/Controller.php';
require_once APP_PATH . '/helper/Model.php';
require_once APP_PATH . '/helper/AuthHelper.php';
require_once APP_PATH . '/helper/Csrf.php';
require_once APP_PATH . '/helper/Validator.php';
require_once APP_PATH . '/helper/Flash.php';

use App\Helpers\Router;

$router = new Router();
require_once BASE_PATH . '/routes/web.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// Strip the base subpath (e.g. /cartly/src/public) so routes are clean
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if ($scriptDir !== '' && strpos($uri, $scriptDir) === 0) {
    $uri = substr($uri, strlen($scriptDir));
}
if ($uri === '' || $uri === false) {
    $uri = '/';
}

$router->dispatch($method, $uri);
