<?php

namespace App\Containers\AppSection\Order\Data\Factories;

use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Models\OrderItem;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Parents\Factories\Factory as ParentFactory;

class OrderItemFactory extends ParentFactory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_name_snapshot' => $this->faker->words(3, true),
            'product_price_snapshot' => 50.00,
            'unit_price' => 50.00,
            'price_override_reason' => null,
            'quantity' => 2,
            'total_item_price' => 100.00,
        ];
    }
}
