<?php

namespace App\Containers\AppSection\Order\Data\Factories;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Order\Models\Order;
use App\Ship\Parents\Factories\Factory as ParentFactory;

class OrderFactory extends ParentFactory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'code' => 'ORD-' . strtoupper($this->faker->unique()->lexify('??????????')),
            'customer_id' => Customer::factory(),
            'customer_name_snapshot' => $this->faker->name(),
            'customer_phone_snapshot' => $this->faker->phoneNumber(),
            'customer_address_snapshot' => $this->faker->address(),
            'delivery_date' => null,
            'shipping_carrier' => null,
            'payment_method' => Order::PAYMENT_METHOD_COD,
            'credit_days' => null,
            'bank_name' => null,
            'bank_account_number' => null,
            'subtotal' => 100.00,
            'shipping_fee' => 0.00,
            'total_amount' => 100.00,
            'advance_payment' => 0.00,
            'remaining_amount' => 100.00,
            'status' => Order::STATUS_PENDING,
            'cancel_reason' => null,
        ];
    }
}
