<?php
declare(strict_types=1);

namespace App\Helpers;

class CartPricing
{
    private const ESTIMATED_DELIVERY_FEE = 0.0;

    public static function estimatedDeliveryFee(array $groups): float
    {
        return $groups === [] ? 0.0 : self::ESTIMATED_DELIVERY_FEE;
    }

    public static function totalWithDelivery(float $subtotal, float $deliveryFee): float
    {
        return round($subtotal + $deliveryFee, 2);
    }
}
