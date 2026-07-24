<?php

namespace App\Containers\AppSection\Order\Data\Factories;

use App\Containers\AppSection\Order\Models\Order;
use App\Ship\Parents\Factories\Factory as ParentFactory;

class OrderFactory extends ParentFactory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'code' => 'ORD-' . strtoupper($this->faker->unique()->lexify('??????????')),
            'customer_name_snapshot' => $this->faker->name(),
            'customer_phone_snapshot' => $this->faker->phoneNumber(),
            'customer_address_snapshot' => $this->faker->address(),
            'payment_method' => 1,
            'subtotal' => 100.00,
            'shipping_fee' => 0.00,
            'total_amount' => 100.00,
            'advance_payment' => 0.00,
            'remaining_amount' => 100.00,
            'status' => 1,
        ];
    }
}
