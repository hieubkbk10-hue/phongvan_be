<?php

namespace App\Containers\AppSection\Media\Data\Factories;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Parents\Factories\Factory as ParentFactory;

class MediaFactory extends ParentFactory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'disk' => 'public',
            'path' => 'products/' . $this->faker->uuid() . '.jpg',
            'filename' => $this->faker->word() . '.jpg',
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(1000, 50000),
            'sort_order' => $this->faker->numberBetween(0, 8),
            'is_main' => false,
            'mediable_type' => Product::class,
            'mediable_id' => Product::factory(),
        ];
    }
}
