<?php
declare(strict_types=1);

namespace App\Helpers;

class Icon
{
    private const ICONS = [
        'admin' => '&#9881;',
        'cart' => '&#128722;',
        'categories' => '&#128451;',
        'dashboard' => '&#128202;',
        'login' => '&#128274;',
        'logout' => '&#10140;',
        'marketplace' => '&#129365;',
        'merchant' => '&#127978;',
        'orders' => '&#128230;',
        'products' => '&#128722;',
        'profile' => '&#128100;',
        'recipes' => '&#127859;',
        'register' => '&#10133;',
        'reports' => '&#9873;',
        'saved' => '&#9825;',
        'store' => '&#127978;',
        'store-profile' => '&#128205;',
        'users' => '&#128101;',
        'vouchers' => '&#127915;',
    ];

    public static function render(string $name, string $class = ''): string
    {
        $className = trim('ui-icon ' . $class);

        return '<span class="' . htmlspecialchars($className, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true">'
            . (self::ICONS[$name] ?? '&#8226;')
            . '</span>';
    }
    public static function storeLogo(array $store, string $class = ''): string
    {
        $storeName = trim((string) ($store['store_name'] ?? 'Store'));
        $logo = trim((string) ($store['store_logo'] ?? ''));
        $className = trim('store-logo ' . $class);

        if ($logo !== '') {
            return '<img class="' . htmlspecialchars($className, ENT_QUOTES, 'UTF-8') . '" src="'
                . htmlspecialchars(UPLOAD_URL . '/' . ltrim($logo, '/'), ENT_QUOTES, 'UTF-8')
                . '" alt="' . htmlspecialchars($storeName . ' logo', ENT_QUOTES, 'UTF-8') . '">';
        }

        $words = preg_split('/\s+/', $storeName) ?: [];
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        if ($initials === '') {
            $initials = 'S';
        }

        $tone = ((int) ($store['store_id'] ?? 0) % 4) + 1;

        return '<span class="' . htmlspecialchars($className . ' store-logo-tone-' . $tone, ENT_QUOTES, 'UTF-8')
            . '" aria-hidden="true">' . htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') . '</span>';
    }
}