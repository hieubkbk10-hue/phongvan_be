<?php

namespace App\Containers\AppSection\Product\UI\API\Tests\Functional;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\UI\API\Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class GetAllProductsTest.
 *
 * @group product
 * @group api
 */
class GetAllProductsTest extends ApiTestCase
{
    protected string $endpoint = 'get@v1/products';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testGetAllProductsSuccess(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->makeCall();

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data', 3)
                ->has('meta.pagination')
                ->etc()
        );
    }

    public function testGetAllProductsLimitExceedsMax100(): void
    {
        $response = $this->endpoint($this->endpoint . '?limit=101')->makeCall();

        $response->assertStatus(422);
    }

    public function testGetAllProductsFilterByStatus(): void
    {
        Product::factory()->create([
            'name' => 'Active Product',
            'status' => Product::STATUS_ACTIVE,
        ]);
        Product::factory()->create([
            'name' => 'Inactive Product',
            'status' => Product::STATUS_INACTIVE,
        ]);

        $response = $this->makeCall(['search' => 'status:' . Product::STATUS_ACTIVE]);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data', 1)
                ->where('data.0.name', 'Active Product')
                ->where('data.0.status', Product::STATUS_ACTIVE)
                ->etc()
        );
    }
}
