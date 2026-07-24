<?php

namespace App\Containers\AppSection\Media\UI\API\Tests\Functional;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\UI\API\Tests\ApiTestCase;
use App\Containers\AppSection\Product\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;

/**
 * Class UploadProductMediaTest.
 *
 * @group media
 * @group api
 */
class UploadProductMediaTest extends ApiTestCase
{
    protected string $endpoint = 'post@v1/products/{product_id}/media';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    protected function uploadMediaCall(Product $product, array $parameters = [], array $files = []): TestResponse
    {
        $user = $this->getTestingUser();
        $token = $user->createToken('token')->accessToken;
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
        $server = $this->transformHeadersToServerVars($headers);

        $url = Config::get('apiato.api.url') . '/v1/products/' . $product->getHashedKey() . '/media';

        $httpResponse = $this->call('POST', $url, $parameters, [], $files, $server);

        return $this->setResponseObjectAndContent($httpResponse);
    }

    public function testUploadFirstPhotoBecomesPrimary(): void
    {
        Storage::fake('public');

        /** @var Product $product */
        $product = Product::factory()->create();

        $file = UploadedFile::fake()->image('photo1.jpg');

        $response = $this->uploadMediaCall($product, [], ['file' => $file]);

        $response->assertStatus(201);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.object', 'Media')
                ->where('data.is_primary', true)
                ->etc()
        );

        $this->assertDatabaseHas('media', [
            'mediable_type' => Product::class,
            'mediable_id' => $product->id,
            'is_main' => true,
        ]);
    }

    public function testUploadTenthPhotoFailsAndCleansFile(): void
    {
        Storage::fake('public');

        /** @var Product $product */
        $product = Product::factory()->create();

        // Create 9 media records for this product
        for ($i = 1; $i <= 9; $i++) {
            Media::create([
                'disk' => 'public',
                'path' => "products/photo{$i}.jpg",
                'filename' => "photo{$i}.jpg",
                'mime_type' => 'image/jpeg',
                'size' => 100,
                'sort_order' => $i,
                'is_main' => ($i === 1),
                'mediable_type' => Product::class,
                'mediable_id' => $product->id,
            ]);
        }

        $file = UploadedFile::fake()->image('photo10.jpg');

        $response = $this->uploadMediaCall($product, [], ['file' => $file]);

        $response->assertStatus(422);

        // Ensure only 9 photos remain in database
        $this->assertEquals(9, Media::where('mediable_type', Product::class)->where('mediable_id', $product->id)->count());
    }

    public function testUploadPhotoWithUnallowedFieldFails(): void
    {
        Storage::fake('public');

        /** @var Product $product */
        $product = Product::factory()->create();

        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->uploadMediaCall($product, ['unallowed_field' => 'test'], ['file' => $file]);

        $response->assertStatus(422);
    }
}
