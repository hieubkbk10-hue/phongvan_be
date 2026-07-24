<?php

namespace App\Containers\AppSection\Customer\UI\API\Tests\Functional;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Customer\UI\API\Tests\ApiTestCase;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class RestoreCustomerTest.
 *
 * @group customer
 * @group api
 */
class RestoreCustomerTest extends ApiTestCase
{
    protected string $endpoint = 'post@v1/customers/{id}/restore';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testRestoreSoftDeletedCustomerSuccess(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        $customer->delete();

        /** @var Customer $freshCustomer */
        $freshCustomer = $customer->fresh();
        $this->assertTrue($freshCustomer->trashed());

        $response = $this->injectId($customer->id)->makeCall();

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.object', 'Customer')
                ->where('data.id', $customer->getHashedKey())
                ->etc()
        );

        /** @var Customer|null $restoredCustomer */
        $restoredCustomer = Customer::find($customer->id);
        $this->assertNotNull($restoredCustomer);
        $this->assertFalse($restoredCustomer->trashed());
    }

    public function testRestoreActiveCustomerReturnsUnprocessableEntity(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();

        $response = $this->injectId($customer->id)->makeCall();

        $response->assertStatus(422);
    }
}
