<?php

namespace App\Containers\AppSection\Order\Tests\Unit;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Order\Tests\TestCase;
use App\Containers\AppSection\Product\Models\Product;
use Illuminate\Support\Facades\Validator;

/**
 * Class OrderRequestValidationTest.
 *
 * @group order
 * @group unit
 */
class OrderRequestValidationTest extends TestCase
{
    public function testCreateOrderValidationRequiresItems(): void
    {
        $rules = [
            'items' => ['required', 'array', 'min:1', 'max:100'],
        ];

        $validator = Validator::make([], $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items', $validator->errors()->toArray());
    }

    public function testCreateOrderValidationPaymentMethodDependencies(): void
    {
        // BANK_TRANSFER (3) requires bank_name and bank_account_number
        $rules = [
            'payment_method' => ['required', 'integer', 'in:1,2,3,4'],
            'bank_name' => ['required_if:payment_method,3', 'nullable', 'string', 'max:100'],
            'bank_account_number' => ['required_if:payment_method,3', 'nullable', 'string', 'max:50'],
            'credit_days' => ['required_if:payment_method,4', 'nullable', 'integer', 'min:1', 'max:365'],
        ];

        $validatorBank = Validator::make(['payment_method' => 3], $rules);
        $this->assertTrue($validatorBank->fails());
        $this->assertArrayHasKey('bank_name', $validatorBank->errors()->toArray());
        $this->assertArrayHasKey('bank_account_number', $validatorBank->errors()->toArray());

        // DEBT (4) requires credit_days
        $validatorDebt = Validator::make(['payment_method' => 4], $rules);
        $this->assertTrue($validatorDebt->fails());
        $this->assertArrayHasKey('credit_days', $validatorDebt->errors()->toArray());
    }

    public function testCreateOrderValidationRequiresCustomerSnapshotsWhenNoCustomerId(): void
    {
        $rules = [
            'customer_id' => ['nullable'],
            'customer_name_snapshot' => ['required_without:customer_id', 'nullable', 'string', 'max:150'],
            'customer_phone_snapshot' => ['required_without:customer_id', 'nullable', 'string', 'regex:/^\+?[0-9\-\s\(\)]{8,20}$/'],
            'customer_address_snapshot' => ['required_without:customer_id', 'nullable', 'string', 'max:500'],
        ];

        $validator = Validator::make([], $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('customer_name_snapshot', $validator->errors()->toArray());
        $this->assertArrayHasKey('customer_phone_snapshot', $validator->errors()->toArray());
        $this->assertArrayHasKey('customer_address_snapshot', $validator->errors()->toArray());

        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        $validatorWithCustomer = Validator::make(['customer_id' => $customer->id], $rules);
        $this->assertFalse($validatorWithCustomer->fails());
    }

    public function testDuplicateProductIdsInItemsRejected(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $rules = [
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.product_id' => ['required', 'distinct'],
        ];

        $payload = [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ];

        $validator = Validator::make($payload, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.1.product_id', $validator->errors()->toArray());
    }
}
