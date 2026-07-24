<?php

namespace App\Containers\AppSection\Product\UI\API\Tests\Functional;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\UI\API\Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class UpdateProductTest.
 *
 * @group product
 * @group api
 */
class UpdateProductTest extends ApiTestCase
{
    protected string $endpoint = 'patch@v1/products/{id}';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testUpdateProductSuccess(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'price' => 100.00,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $data = [
            'name' => 'Updated Name',
            'price' => 150.00,
        ];

        $response = $this->injectId($product->id)->makeCall($data);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.object', 'Product')
                ->where('data.name', 'Updated Name')
                ->where('data.price', '150.00')
                ->where('data.status', Product::STATUS_ACTIVE)
                ->etc()
        );
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'price' => 150.00,
        ]);
    }

    public function testDeactivateProduct(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create([
            'status' => Product::STATUS_ACTIVE,
        ]);

        $data = [
            'status' => Product::STATUS_INACTIVE,
        ];

        $response = $this->injectId($product->id)->makeCall($data);

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', Product::STATUS_INACTIVE);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => Product::STATUS_INACTIVE,
        ]);
    }

    public function testActivateProduct(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create([
            'status' => Product::STATUS_INACTIVE,
        ]);

        $data = [
            'status' => Product::STATUS_ACTIVE,
        ];

        $response = $this->injectId($product->id)->makeCall($data);

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', Product::STATUS_ACTIVE);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => Product::STATUS_ACTIVE,
        ]);
    }

    public function testUpdateProductRejectsUnallowedField(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $data = [
            'extra_field' => 'value',
        ];

        $response = $this->injectId($product->id)->makeCall($data);

        $response->assertStatus(422);
    }

    public function testUpdateProductNonExistingId(): void
    {
        $invalidId = 999999;

        $response = $this->injectId($invalidId)->makeCall(['name' => 'New Name']);

        $response->assertStatus(422);
    }
}
