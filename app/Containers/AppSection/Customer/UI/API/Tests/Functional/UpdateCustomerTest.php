<?php

namespace App\Containers\AppSection\Customer\UI\API\Tests\Functional;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Customer\UI\API\Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class UpdateCustomerTest.
 *
 * @group customer
 * @group api
 */
class UpdateCustomerTest extends ApiTestCase
{
    protected string $endpoint = 'patch@v1/customers/{id}';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testUpdateCustomerSuccess(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create([
            'name' => 'Original Name',
            'phone' => '+84901234567',
        ]);

        $data = [
            'name' => 'Updated Name',
        ];

        $response = $this->injectId($customer->id)->makeCall($data);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.object', 'Customer')
                ->where('data.name', 'Updated Name')
                ->where('data.phone', '+84901234567')
                ->etc()
        );
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Name',
        ]);
    }

    public function testUpdateCustomerSamePhoneNoDuplicateError(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create([
            'name' => 'Original Name',
            'phone' => '+84901234567',
        ]);

        $data = [
            'name' => 'New Name',
            'phone' => '+84901234567',
        ];

        $response = $this->injectId($customer->id)->makeCall($data);

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'New Name');
    }

    public function testUpdateCustomerDuplicatePhoneWithAnotherCustomer(): void
    {
        Customer::factory()->create([
            'phone' => '+84901111111',
        ]);
        /** @var Customer $customer2 */
        $customer2 = Customer::factory()->create([
            'phone' => '+84902222222',
        ]);

        $data = [
            'phone' => '+84901111111',
        ];

        $response = $this->injectId($customer2->id)->makeCall($data);

        $response->assertStatus(422);
    }

    public function testUpdateCustomerRejectsUnallowedField(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();

        $data = [
            'extra_field' => 'value',
        ];

        $response = $this->injectId($customer->id)->makeCall($data);

        $response->assertStatus(422);
    }

    public function testUpdateCustomerNonExistingId(): void
    {
        $invalidId = 999999;

        $response = $this->injectId($invalidId)->makeCall(['name' => 'New Name']);

        $response->assertStatus(422);
    }
}
