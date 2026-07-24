<?php

namespace App\Containers\AppSection\Product\UI\API\Tests\Functional;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\UI\API\Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class FindProductByIdTest.
 *
 * @group product
 * @group api
 */
class FindProductByIdTest extends ApiTestCase
{
    protected string $endpoint = 'get@v1/products/{id}';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testFindProductByIdSuccess(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create([
            'price' => 250.75,
        ]);

        $response = $this->injectId($product->id)->makeCall();

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.object', 'Product')
                ->where('data.id', $product->getHashedKey())
                ->where('data.name', $product->name)
                ->where('data.price', '250.75')
                ->where('data.status', (int) $product->status)
                ->etc()
        );
    }

    public function testFindProductByNonExistingId(): void
    {
        $invalidId = 999999;

        $response = $this->injectId($invalidId)->makeCall();

        $response->assertStatus(422);
    }
}
