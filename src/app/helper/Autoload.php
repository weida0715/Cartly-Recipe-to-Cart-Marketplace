<?php
declare(strict_types=1);

/**
 * Simple PSR-4-ish autoloader.
 *   App\Controllers\X  -> src/app/controllers/X.php
 *   App\Models\X       -> src/app/models/X.php
 *   App\Helpers\X      -> src/app/helper/X.php
 */
spl_autoload_register(function (string $class): void {
    if (strpos($class, 'App\\') !== 0) {
        return;
    }
    $parts = explode('\\', substr($class, 4));
    $map = [
        'Controllers' => 'controllers',
        'Models'      => 'models',
        'Helpers'     => 'helper',
    ];
    $top = array_shift($parts);
    if (!isset($map[$top])) {
        return;
    }
    $rel = $map[$top] . '/' . implode('/', $parts) . '.php';
    $path = dirname(__DIR__) . '/' . $rel;

    if (is_file($path)) {
        require_once $path;
        return;
    }

    // Fallback: lowercase directories while preserving the class filename.
    $dirParts = array_slice($parts, 0, -1);
    $file = end($parts);
    if ($dirParts) {
        $relLower = $map[$top] . '/' . strtolower(implode('/', $dirParts)) . '/' . $file . '.php';
        $pathLower = dirname(__DIR__) . '/' . $relLower;
        if (is_file($pathLower)) {
            require_once $pathLower;
        }
    }
});
