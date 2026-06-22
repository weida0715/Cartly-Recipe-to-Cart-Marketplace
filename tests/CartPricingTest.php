<?php
declare(strict_types=1);

use App\Helpers\CartPricing;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';

class CartPricingTest extends TestCase
{
    public function test_delivery_fee_defaults_to_zero_for_current_cart_policy(): void
    {
        $groups = [
            1 => ['subtotal' => 25.50],
        ];

        $deliveryFee = CartPricing::estimatedDeliveryFee($groups);

        $this->assertSame(0.0, $deliveryFee);
        $this->assertSame(25.50, CartPricing::totalWithDelivery(25.50, $deliveryFee));
    }
}
