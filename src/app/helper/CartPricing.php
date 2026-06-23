<?php
declare(strict_types=1);

namespace App\Helpers;

class CartPricing
{
    private const DELIVERY_FEE_PER_STORE = 2.0;

    public static function deliveryFeeForGroups(array $groups, float $feePerStore): float
    {
        return round(count($groups) * max(0, $feePerStore), 2);
    }

    public static function deliveryFeePerStore(): float
    {
        return self::DELIVERY_FEE_PER_STORE;
    }

    public static function estimatedDeliveryFee(array $groups): float
    {
        return round(count($groups) * self::DELIVERY_FEE_PER_STORE, 2);
    }

    public static function totalWithDelivery(float $subtotal, float $deliveryFee): float
    {
        return round(max(0, $subtotal) + max(0, $deliveryFee), 2);
    }

    public static function merchantTotal(float $subtotal, float $discount, float $deliveryFee): float
    {
        return round(max(0, $subtotal - $discount) + max(0, $deliveryFee), 2);
    }
}
