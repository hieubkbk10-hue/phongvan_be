<?php

namespace App\Containers\AppSection\Media\UI\API\Tests\Functional;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\UI\API\Tests\ApiTestCase;
use App\Containers\AppSection\Product\Models\Product;

/**
 * Class SetPrimaryMediaTest.
 *
 * @group media
 * @group api
 */
class SetPrimaryMediaTest extends ApiTestCase
{
    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testSetPrimaryMediaUpdatesOldPrimary(): void
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

        $url = "patch@v1/products/{$product->getHashedKey()}/media/{$media2->getHashedKey()}/primary";

        $response = $this->endpoint($url)->makeCall();

        $response->assertStatus(200);
        $response->assertJsonPath('data.is_primary', true);

        // Media 1 should no longer be primary
        $this->assertDatabaseHas('media', [
            'id' => $media1->id,
            'is_main' => false,
        ]);

        // Media 2 should now be primary
        $this->assertDatabaseHas('media', [
            'id' => $media2->id,
            'is_main' => true,
        ]);
    }

    public function testSetPrimaryMediaOfOtherProductFails(): void
    {
        /** @var Product $product1 */
        $product1 = Product::factory()->create();
        /** @var Product $product2 */
        $product2 = Product::factory()->create();

        /** @var Media $mediaOfProduct2 */
        $mediaOfProduct2 = Media::create([
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

        // Attempt to set primary for product1 using product2's media ID
        $url = "patch@v1/products/{$product1->getHashedKey()}/media/{$mediaOfProduct2->getHashedKey()}/primary";

        $response = $this->endpoint($url)->makeCall();

        $response->assertStatus(422);
    }
}
