<?php
declare(strict_types=1);

use App\Helpers\CartPricing;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';

class CartPricingTest extends TestCase
{
    public function test_delivery_fee_is_fixed_amount_per_store(): void
    {
        $groups = [
            1 => ['subtotal' => 25.50],
            2 => ['subtotal' => 10.00],
            3 => ['subtotal' => 14.50],
        ];

        $deliveryFee = CartPricing::estimatedDeliveryFee($groups);

        $this->assertSame(15.0, $deliveryFee);
        $this->assertSame(65.0, CartPricing::totalWithDelivery(50.0, $deliveryFee));
    }
}
