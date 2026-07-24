<?php

namespace App\Containers\AppSection\Product\Data\Factories;

use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Parents\Factories\Factory as ParentFactory;

class ProductFactory extends ParentFactory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'status' => 1,
        ];
    }
}

