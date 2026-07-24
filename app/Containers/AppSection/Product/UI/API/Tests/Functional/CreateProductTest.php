<?php

namespace App\Containers\AppSection\Product\UI\API\Tests\Functional;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\UI\API\Tests\ApiTestCase;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class CreateProductTest.
 *
 * @group product
 * @group api
 */
class CreateProductTest extends ApiTestCase
{
    protected string $endpoint = 'post@v1/products';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testCreateProductSuccess(): void
    {
        $data = [
            'name' => 'Laptop Dell XPS 15',
            'price' => 1500.5,
            'status' => Product::STATUS_ACTIVE,
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(201);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.object', 'Product')
                ->where('data.name', $data['name'])
                ->where('data.price', '1500.50')
                ->where('data.status', Product::STATUS_ACTIVE)
                ->etc()
        );
        $this->assertDatabaseHas('products', [
            'name' => $data['name'],
            'price' => 1500.50,
            'status' => Product::STATUS_ACTIVE,
        ]);
    }

    public function testCreateProductDefaultStatusActive(): void
    {
        $data = [
            'name' => 'Mouse Logitech MX Master 3',
            'price' => 99.99,
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(201);
        $response->assertJsonPath('data.status', Product::STATUS_ACTIVE);
        $response->assertJsonPath('data.price', '99.99');
    }

    public function testCreateProductRejectsNegativePrice(): void
    {
        $data = [
            'name' => 'Invalid Product',
            'price' => -10.50,
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(422);
    }

    public function testCreateProductRejectsInvalidStatus(): void
    {
        $data = [
            'name' => 'Invalid Status Product',
            'price' => 50,
            'status' => 99,
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(422);
    }

    public function testCreateProductRejectsUnallowedField(): void
    {
        $data = [
            'name' => 'Product with extra field',
            'price' => 100,
            'extra_field' => 'unallowed',
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(422);
    }

    public function testCreateProductWithoutAuth(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->testingUser = $user;

        $data = [
            'name' => 'Unauth Product',
            'price' => 10,
        ];

        $response = $this->auth(false)->makeCall($data);

        $response->assertStatus(401);
    }
}
