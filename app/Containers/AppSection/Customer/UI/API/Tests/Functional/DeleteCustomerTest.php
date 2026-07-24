<?php

namespace App\Containers\AppSection\Customer\UI\API\Tests\Functional;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Customer\UI\API\Tests\ApiTestCase;

/**
 * Class DeleteCustomerTest.
 *
 * @group customer
 * @group api
 */
class DeleteCustomerTest extends ApiTestCase
{
    protected string $endpoint = 'delete@v1/customers/{id}';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testDeleteCustomerSoftDeletesRecord(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();

        $response = $this->injectId($customer->id)->makeCall();

        $response->assertStatus(204);

        // Record must not be hard deleted from database
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
        ]);

        // Record must be soft deleted (deleted_at IS NOT NULL)
        /** @var Customer|null $trashedCustomer */
        $trashedCustomer = Customer::withTrashed()->find($customer->id);
        $this->assertNotNull($trashedCustomer);
        $this->assertTrue($trashedCustomer->trashed());
    }

    public function testDeleteNonExistingCustomer(): void
    {
        $invalidId = 999999;

        $response = $this->injectId($invalidId)->makeCall();

        $response->assertStatus(422);
    }
}
