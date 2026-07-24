<?php

namespace App\Containers\AppSection\Customer\Data\Factories;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Ship\Parents\Factories\Factory as ParentFactory;

class CustomerFactory extends ParentFactory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => '+84' . $this->faker->unique()->numerify('9########'),
            'address' => $this->faker->address(),
            'email' => $this->faker->safeEmail(),
        ];
    }
}
