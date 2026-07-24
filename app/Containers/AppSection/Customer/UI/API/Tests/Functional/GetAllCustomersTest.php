<?php

namespace App\Containers\AppSection\Customer\UI\API\Tests\Functional;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Customer\UI\API\Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class GetAllCustomersTest.
 *
 * @group customer
 * @group api
 */
class GetAllCustomersTest extends ApiTestCase
{
    protected string $endpoint = 'get@v1/customers';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testGetAllCustomersSuccess(): void
    {
        Customer::factory()->count(3)->create();

        $response = $this->makeCall();

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data', 3)
                ->has('meta.pagination')
                ->etc()
        );
    }

    public function testGetAllCustomersLimitExceedsMax100(): void
    {
        $response = $this->endpoint($this->endpoint . '?limit=101')->makeCall();

        $response->assertStatus(422);
    }

    public function testGetAllCustomersPhoneExactSearch(): void
    {
        Customer::factory()->create([
            'phone' => '+84901234567',
        ]);
        Customer::factory()->create([
            'phone' => '+84901234568',
        ]);

        $response = $this->makeCall(['search' => 'phone:+84901234567']);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data', 1)
                ->where('data.0.phone', '+84901234567')
                ->etc()
        );
    }
}
