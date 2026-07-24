<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Tasks\CreateMediaTask;
use App\Containers\AppSection\Media\Tests\TestCase;
use App\Containers\AppSection\Product\Models\Product;

/**
 * Class CreateMediaTaskTest.
 *
 * @group media
 * @group unit
 */
class CreateMediaTaskTest extends TestCase
{
    public function testCreateMedia(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $data = [
            'disk' => 'public',
            'path' => 'products/sample.jpg',
            'filename' => 'sample.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'sort_order' => 1,
            'is_main' => true,
            'mediable_type' => Product::class,
            'mediable_id' => $product->id,
        ];

        $media = app(CreateMediaTask::class)->run($data);

        $this->assertModelExists($media);
    }
}
