<?php
declare(strict_types=1);

namespace App\Helpers;

final class CartVoucherSession
{
    private const KEY = 'cart_vouchers';

    public static function all(int $userId, int $cartId): array
    {
        $stored = $_SESSION[self::KEY][(string) $userId][(string) $cartId] ?? [];
        if (!is_array($stored)) {
            return [];
        }

        $result = [];
        foreach ($stored as $storeId => $codes) {
            if (!is_array($codes)) {
                continue;
            }
            $normalized = array_values(array_unique(array_filter(array_map(
                static fn($code): string => strtoupper(trim((string) $code)),
                $codes
            ), static fn(string $code): bool => $code !== '')));
            if ($normalized) {
                $result[(int) $storeId] = $normalized;
            }
        }
        return $result;
    }

    public static function add(int $userId, int $cartId, int $storeId, string $code): bool
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return false;
        }

        $all = self::all($userId, $cartId);
        $codes = $all[$storeId] ?? [];
        if (in_array($code, $codes, true)) {
            return false;
        }
        $codes[] = $code;
        self::replaceStore($userId, $cartId, $storeId, $codes);
        return true;
    }

    public static function remove(int $userId, int $cartId, int $storeId, string $code): void
    {
        $code = strtoupper(trim($code));
        $codes = array_values(array_filter(
            self::all($userId, $cartId)[$storeId] ?? [],
            static fn(string $storedCode): bool => $storedCode !== $code
        ));
        self::replaceStore($userId, $cartId, $storeId, $codes);
    }

    public static function replaceStore(int $userId, int $cartId, int $storeId, array $codes): void
    {
        $normalized = array_values(array_unique(array_filter(array_map(
            static fn($code): string => strtoupper(trim((string) $code)),
            $codes
        ), static fn(string $code): bool => $code !== '')));

        if (!$normalized) {
            unset($_SESSION[self::KEY][(string) $userId][(string) $cartId][(string) $storeId]);
            return;
        }
        $_SESSION[self::KEY][(string) $userId][(string) $cartId][(string) $storeId] = $normalized;
    }

    public static function clear(int $userId, int $cartId): void
    {
        unset($_SESSION[self::KEY][(string) $userId][(string) $cartId]);
    }
}
