<?php

namespace App\Containers\AppSection\Customer\UI\API\Tests\Functional;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Customer\UI\API\Tests\ApiTestCase;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class CreateCustomerTest.
 *
 * @group customer
 * @group api
 */
class CreateCustomerTest extends ApiTestCase
{
    protected string $endpoint = 'post@v1/customers';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testCreateCustomerSuccess(): void
    {
        $data = [
            'name' => 'Nguyen Van A',
            'phone' => '+84901234567',
            'address' => '123 Tran Hung Dao, Quan 1, TP.HCM',
            'email' => 'nguyenvana@example.com',
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(201);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.object', 'Customer')
                ->where('data.name', $data['name'])
                ->where('data.phone', $data['phone'])
                ->where('data.address', $data['address'])
                ->where('data.email', $data['email'])
                ->etc()
        );
        $this->assertDatabaseHas('customers', [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'email' => $data['email'],
        ]);
    }

    public function testCreateCustomerNormalizesPhone(): void
    {
        $data = [
            'name' => 'Nguyen Van B',
            'phone' => '+84 90-123(45)67',
            'address' => '456 Le Loi',
            'email' => 'nguyenvanb@example.com',
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(201);
        $response->assertJsonPath('data.phone', '+84901234567');
        $this->assertDatabaseHas('customers', [
            'phone' => '+84901234567',
        ]);
    }

    public function testCreateCustomerRejectsInvalidE164Phone(): void
    {
        $data = [
            'name' => 'Nguyen Van C',
            'phone' => '0901234567',
            'address' => '789 Nguyen Hue',
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(422);
    }

    public function testCreateCustomerDuplicatePhoneWithActiveCustomer(): void
    {
        Customer::factory()->create([
            'phone' => '+84901234567',
        ]);

        $data = [
            'name' => 'Nguyen Van D',
            'phone' => '+84901234567',
            'address' => '100 Hai Ba Trung',
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(422);
    }

    public function testCreateCustomerDuplicatePhoneWithSoftDeletedCustomer(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create([
            'phone' => '+84901234567',
        ]);
        $customer->delete();

        $data = [
            'name' => 'Nguyen Van E',
            'phone' => '+84901234567',
            'address' => '200 Ly Tu Trong',
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(422);
    }

    public function testCreateCustomerRejectsUnallowedField(): void
    {
        $data = [
            'name' => 'Nguyen Van F',
            'phone' => '+84901234567',
            'address' => '300 Pasteur',
            'extra_field' => 'not_allowed',
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(422);
    }

    public function testCreateCustomerExceedsMaxLength(): void
    {
        $data = [
            'name' => str_repeat('a', 151),
            'phone' => '+84901234567',
            'address' => '400 Nam Ky Khoi Nghia',
        ];

        $response = $this->makeCall($data);

        $response->assertStatus(422);
    }

    public function testCreateCustomerWithoutAuth(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->testingUser = $user;

        $data = [
            'name' => 'Nguyen Van G',
            'phone' => '+84901234567',
            'address' => '500 Vo Van Kiet',
        ];

        $response = $this->auth(false)->makeCall($data);

        $response->assertStatus(401);
    }
}
