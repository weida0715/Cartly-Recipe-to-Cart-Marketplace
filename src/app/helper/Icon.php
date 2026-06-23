<?php
declare(strict_types=1);

namespace App\Helpers;

class Icon
{
    private const ICONS = [
        'admin' => 'settings',
        'cart' => 'shopping-cart',
        'categories' => 'category',
        'dashboard' => 'apps',
        'login' => 'sign-in-alt',
        'logout' => 'sign-out-alt',
        'marketplace' => 'carrot',
        'merchant' => 'shop',
        'orders' => 'box',
        'products' => 'shopping-cart',
        'profile' => 'user',
        'recipes' => 'restaurant',
        'register' => 'user-add',
        'reports' => 'flag',
        'saved' => 'heart',
        'settings' => 'settings',
        'store' => 'shop',
        'store-profile' => 'marker',
        'users' => 'users',
        'vouchers' => 'ticket',
    ];

    public static function render(string $name, string $class = ''): string
    {
        $iconName = self::ICONS[$name] ?? 'apps';
        $className = trim('ui-icon fi fi-rr-' . $iconName . ' ' . $class);

        return '<i class="' . htmlspecialchars($className, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true"></i>';
    }

    public static function storeLogo(array $store, string $class = ''): string
    {
        $storeName = trim((string) ($store['store_name'] ?? 'Store'));
        $logo = str_replace('\\', '/', trim((string) ($store['store_logo'] ?? '')));
        $className = trim('store-logo ' . $class);

        if (preg_match('#\Astores/logos/[a-f0-9]{32}\.(?:jpg|png|webp|gif)\z#i', $logo) === 1) {
            $fullLogoPath = UPLOAD_URL . '/' . $logo;

            return '<img class="' . htmlspecialchars($className, ENT_QUOTES, 'UTF-8') . '" src="'
                . htmlspecialchars($fullLogoPath, ENT_QUOTES, 'UTF-8')
                . '" alt="' . htmlspecialchars($storeName . ' logo', ENT_QUOTES, 'UTF-8') . '">';
        }

        $words = preg_split('/\s+/u', $storeName) ?: [];
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
            } else {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        if ($initials === '') {
            $initials = 'S';
        }

        $tone = (abs((int) ($store['store_id'] ?? 0)) % 4) + 1;

        return '<span class="' . htmlspecialchars($className . ' store-logo-tone-' . $tone, ENT_QUOTES, 'UTF-8')
            . '" aria-hidden="true">' . htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') . '</span>';
    }
}
