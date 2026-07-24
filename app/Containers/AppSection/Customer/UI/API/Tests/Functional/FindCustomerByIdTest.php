<?php

namespace App\Containers\AppSection\Customer\UI\API\Tests\Functional;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Customer\UI\API\Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class FindCustomerByIdTest.
 *
 * @group customer
 * @group api
 */
class FindCustomerByIdTest extends ApiTestCase
{
    protected string $endpoint = 'get@v1/customers/{id}';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testFindCustomerByIdSuccess(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();

        $response = $this->injectId($customer->id)->makeCall();

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.object', 'Customer')
                ->where('data.id', $customer->getHashedKey())
                ->where('data.name', $customer->name)
                ->where('data.phone', $customer->phone)
                ->where('data.address', $customer->address)
                ->where('data.email', $customer->email)
                ->etc()
        );
    }

    public function testFindCustomerByNonExistingId(): void
    {
        $invalidId = 999999;

        $response = $this->injectId($invalidId)->makeCall();

        $response->assertStatus(422);
    }

    public function testFindCustomerSoftDeletedReturnsNotFound(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        $customer->delete();

        $response = $this->injectId($customer->id)->makeCall();

        $response->assertStatus(422);
    }
}
