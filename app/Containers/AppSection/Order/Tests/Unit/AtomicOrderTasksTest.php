<?php

namespace App\Containers\AppSection\Order\Tests\Unit;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Tasks\CalculateOrderTotalsTask;
use App\Containers\AppSection\Order\Tasks\CancelOrderTask;
use App\Containers\AppSection\Order\Tasks\CompleteOrderTask;
use App\Containers\AppSection\Order\Tasks\GenerateOrderCodeTask;
use App\Containers\AppSection\Order\Tasks\GetActiveProductsByIdsTask;
use App\Containers\AppSection\Order\Tasks\ResolveOrderCustomerTask;
use App\Containers\AppSection\Order\Tests\TestCase;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Exceptions\ValidationFailedException;

/**
 * Class AtomicOrderTasksTest.
 *
 * @group order
 * @group unit
 */
class AtomicOrderTasksTest extends TestCase
{
    public function testResolveOrderCustomerTaskCreatesNewCustomerOrUsesExisting(): void
    {
        /** @var Customer $existingCustomer */
        $existingCustomer = Customer::factory()->create(['phone' => '0901234567']);

        // Case 1: Existing Customer ID
        $resolved1 = app(ResolveOrderCustomerTask::class)->run($existingCustomer->id);
        $this->assertEquals($existingCustomer->id, $resolved1['customer_id']);
        $this->assertEquals($existingCustomer->name, $resolved1['name_snapshot']);

        // Case 2: Duplicate Phone throws ValidationFailedException
        $this->expectException(ValidationFailedException::class);
        app(ResolveOrderCustomerTask::class)->run(null, 'New Guy', '0901234567', 'Addr');
    }

    public function testResolveOrderCustomerTaskCreatesNewCustomerWhenPhoneUnique(): void
    {
        $resolved = app(ResolveOrderCustomerTask::class)->run(null, 'Brand New Customer', '0988776655', '123 New St');
        $this->assertNotNull($resolved['customer_id']);
        $this->assertEquals('Brand New Customer', $resolved['name_snapshot']);
        $this->assertEquals('0988776655', $resolved['phone_snapshot']);
        $this->assertDatabaseHas('customers', ['phone' => '0988776655']);
    }

    public function testGetActiveProductsByIdsTaskBatchLoadsAndRejectsInactive(): void
    {
        /** @var Product $activeProd1 */
        $activeProd1 = Product::factory()->create(['status' => Product::STATUS_ACTIVE]);
        /** @var Product $activeProd2 */
        $activeProd2 = Product::factory()->create(['status' => Product::STATUS_ACTIVE]);
        /** @var Product $inactiveProd */
        $inactiveProd = Product::factory()->create(['status' => Product::STATUS_INACTIVE]);

        $products = app(GetActiveProductsByIdsTask::class)->run([$activeProd1->id, $activeProd2->id]);
        $this->assertCount(2, $products);
        $this->assertTrue($products->has($activeProd1->id));

        $this->expectException(ValidationFailedException::class);
        app(GetActiveProductsByIdsTask::class)->run([$activeProd1->id, $inactiveProd->id]);
    }

    public function testGenerateOrderCodeTaskReturnsUniqueCode(): void
    {
        $code = app(GenerateOrderCodeTask::class)->run();
        $this->assertStringStartsWith('ORD-', $code);
    }

    public function testCalculateOrderTotalsTaskComputesMoneyAndPriceOverride(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create(['price' => '100.00', 'status' => Product::STATUS_ACTIVE]);
        $products = collect([$product->id => $product]);

        $itemsInput = [
            [
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_price' => '90.00',
                'price_override_reason' => 'VIP Discount',
            ],
        ];

        $totals = app(CalculateOrderTotalsTask::class)->run($itemsInput, $products, '15.00', '50.00');

        $this->assertEquals('180.00', $totals['subtotal']);
        $this->assertEquals('15.00', $totals['shipping_fee']);
        $this->assertEquals('195.00', $totals['total_amount']);
        $this->assertEquals('50.00', $totals['advance_payment']);
        $this->assertEquals('145.00', $totals['remaining_amount']);

        $item = $totals['processed_items'][0];
        $this->assertEquals('100.00', $item['product_price_snapshot']);
        $this->assertEquals('90.00', $item['unit_price']);
        $this->assertEquals('VIP Discount', $item['price_override_reason']);
        $this->assertEquals('180.00', $item['total_item_price']);
    }

    public function testCalculateOrderTotalsTaskRequiresReasonWhenPriceDiffers(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create(['price' => '100.00', 'status' => Product::STATUS_ACTIVE]);
        $products = collect([$product->id => $product]);

        $itemsInput = [
            [
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => '80.00',
                'price_override_reason' => '',
            ],
        ];

        $this->expectException(ValidationFailedException::class);
        app(CalculateOrderTotalsTask::class)->run($itemsInput, $products, '0.00', '0.00');
    }

    public function testCalculateOrderTotalsTaskRejectsAdvanceExceedingTotal(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create(['price' => '100.00', 'status' => Product::STATUS_ACTIVE]);
        $products = collect([$product->id => $product]);

        $itemsInput = [
            [
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => '100.00',
            ],
        ];

        $this->expectException(ValidationFailedException::class);
        app(CalculateOrderTotalsTask::class)->run($itemsInput, $products, '0.00', '150.00');
    }

    public function testCompleteOrderTaskRequiresDeliveryDateAndCarrier(): void
    {
        /** @var Order $order */
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'delivery_date' => '2026-08-01',
            'shipping_carrier' => 'Express',
        ]);

        $completed = app(CompleteOrderTask::class)->run($order);
        $this->assertEquals(Order::STATUS_COMPLETED, $completed->status);
    }

    public function testCancelOrderTaskRequiresReasonAndPendingStatus(): void
    {
        /** @var Order $order */
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $cancelled = app(CancelOrderTask::class)->run($order, 'Customer changed mind');
        $this->assertEquals(Order::STATUS_CANCELLED, $cancelled->status);
        $this->assertEquals('Customer changed mind', $cancelled->cancel_reason);
    }
}
