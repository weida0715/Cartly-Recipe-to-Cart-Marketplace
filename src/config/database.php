<?php
declare(strict_types=1);

// MySQL connection settings.
if (!defined('DB_HOST')) {
    $env = static function (string $key, string $default = ''): string {
        $value = $_ENV[$key] ?? getenv($key);
        return $value !== false && $value !== '' ? (string) $value : $default;
    };

    define('DB_HOST', $env('DB_HOST', '127.0.0.1'));
    define('DB_PORT', $env('DB_PORT', '3306'));
    define('DB_NAME', $env('DB_NAME', 'cartly'));
    define('DB_USER', $env('DB_USER', 'root'));
    define('DB_PASS', $env('DB_PASS', $env('DB_PASSWORD')));
    define('DB_CHARSET', $env('DB_CHARSET', 'utf8mb4'));
}

/**
 * Returns a singleton PDO instance.
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
