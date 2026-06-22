<?php
declare(strict_types=1);

namespace App\Helpers;

class CartPricing
{
    private const DELIVERY_FEE_PER_STORE = 5.0;

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
        return round($subtotal + $deliveryFee, 2);
    }
}
