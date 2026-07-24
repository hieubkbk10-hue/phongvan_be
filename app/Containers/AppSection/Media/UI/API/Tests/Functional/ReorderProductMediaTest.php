<?php

namespace App\Containers\AppSection\Media\UI\API\Tests\Functional;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\UI\API\Tests\ApiTestCase;
use App\Containers\AppSection\Product\Models\Product;

/**
 * Class ReorderProductMediaTest.
 *
 * @group media
 * @group api
 */
class ReorderProductMediaTest extends ApiTestCase
{
    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testReorderProductMediaSuccess(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var Media $media1 */
        $media1 = Media::create([
            'disk' => 'public',
            'path' => 'products/photo1.jpg',
            'filename' => 'photo1.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'sort_order' => 1,
            'is_main' => true,
            'mediable_type' => Product::class,
            'mediable_id' => $product->id,
        ]);

        /** @var Media $media2 */
        $media2 = Media::create([
            'disk' => 'public',
            'path' => 'products/photo2.jpg',
            'filename' => 'photo2.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'sort_order' => 2,
            'is_main' => false,
            'mediable_type' => Product::class,
            'mediable_id' => $product->id,
        ]);

        $url = "post@v1/products/{$product->getHashedKey()}/media/reorder";

        $data = [
            'media' => [
                ['id' => $media1->getHashedKey(), 'sort_order' => 10],
                ['id' => $media2->getHashedKey(), 'sort_order' => 5],
            ],
        ];

        $response = $this->endpoint($url)->makeCall($data);

        $response->assertStatus(202);

        $this->assertDatabaseHas('media', [
            'id' => $media1->id,
            'sort_order' => 10,
        ]);
        $this->assertDatabaseHas('media', [
            'id' => $media2->id,
            'sort_order' => 5,
        ]);
    }

    public function testReorderProductMediaWithCrossProductFails(): void
    {
        /** @var Product $product1 */
        $product1 = Product::factory()->create();
        /** @var Product $product2 */
        $product2 = Product::factory()->create();

        /** @var Media $media1 */
        $media1 = Media::create([
            'disk' => 'public',
            'path' => 'products/photo1.jpg',
            'filename' => 'photo1.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'sort_order' => 1,
            'is_main' => true,
            'mediable_type' => Product::class,
            'mediable_id' => $product1->id,
        ]);

        /** @var Media $mediaOther */
        $mediaOther = Media::create([
            'disk' => 'public',
            'path' => 'products/photo2.jpg',
            'filename' => 'photo2.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'sort_order' => 1,
            'is_main' => true,
            'mediable_type' => Product::class,
            'mediable_id' => $product2->id,
        ]);

        $url = "post@v1/products/{$product1->getHashedKey()}/media/reorder";

        $data = [
            'media' => [
                ['id' => $media1->getHashedKey(), 'sort_order' => 1],
                ['id' => $mediaOther->getHashedKey(), 'sort_order' => 2],
            ],
        ];

        $response = $this->endpoint($url)->makeCall($data);

        $response->assertStatus(422);
    }
}
