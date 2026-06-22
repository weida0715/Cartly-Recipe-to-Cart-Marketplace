<?php
declare(strict_types=1);

if (!function_exists('cartly_load_env')) {
    function cartly_load_env(?string $preferredPath = null): ?string
    {
        $candidates = array_values(array_filter([
            $preferredPath,
            getenv('CARTLY_ENV_FILE') ?: null,
            dirname(__DIR__, 2) . '/.env',
        ]));

        foreach ($candidates as $path) {
            if (!is_file($path) || !is_readable($path)) {
                continue;
            }

            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false) {
                continue;
            }

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }

                [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
                $name = trim($name);
                if ($name === '') {
                    continue;
                }

                $value = trim($value);
                if (
                    strlen($value) >= 2
                    && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))
                ) {
                    $value = substr($value, 1, -1);
                }

                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
                putenv($name . '=' . $value);
            }

            return $path;
        }

        return null;
    }
}

if (!function_exists('cartly_env')) {
    function cartly_env(string $key, string $default = ''): string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return $value !== false && $value !== null && $value !== '' ? (string) $value : $default;
    }
}

if (!function_exists('cartly_normalize_base_path')) {
    function cartly_normalize_base_path(?string $basePath): string
    {
        $basePath = trim((string) $basePath);
        if ($basePath === '' || $basePath === '/') {
            return '';
        }

        return '/' . trim($basePath, '/');
    }
}

if (!function_exists('cartly_detect_script_base_url')) {
    function cartly_detect_script_base_url(): string
    {
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        return $scriptDir === '/' ? '' : $scriptDir;
    }
}

if (!function_exists('cartly_app_base_url')) {
    function cartly_app_base_url(): string
    {
        $configured = cartly_normalize_base_path(cartly_env('APP_BASE_PATH'));
        if ($configured !== '') {
            return $configured;
        }

        return cartly_normalize_base_path(cartly_detect_script_base_url());
    }
}
