<?php

namespace App\Containers\AppSection\Order\Tests\Unit;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Models\OrderItem;
use App\Containers\AppSection\Order\Tasks\FindOrderByIdTask;
use App\Containers\AppSection\Order\Tasks\GetAllOrdersTask;
use App\Containers\AppSection\Order\Tests\TestCase;
use App\Containers\AppSection\Order\UI\API\Transformers\OrderItemTransformer;
use App\Containers\AppSection\Order\UI\API\Transformers\OrderTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

/**
 * @group order
 * @group unit
 */
class OrderTransformerTest extends TestCase
{
    public function testOrderTransformerReturnsOrderContract(): void
    {
        /** @var Order $order */
        $order = Order::factory()->create([
            'code' => 'ORD-TRANSFORMER',
            'customer_name_snapshot' => 'Customer Name',
            'customer_phone_snapshot' => '+84901234567',
            'customer_address_snapshot' => 'Customer Address',
            'delivery_date' => '2026-07-30',
            'shipping_carrier' => 'Carrier',
            'payment_method' => 3,
            'bank_name' => 'Test Bank',
            'bank_account_number' => '123456789',
            'subtotal' => '100.00',
            'shipping_fee' => '10.00',
            'total_amount' => '110.00',
            'advance_payment' => '20.00',
            'remaining_amount' => '90.00',
            'status' => 1,
        ]);

        $response = (new OrderTransformer())->transform($order);

        $this->assertSame('ORD-TRANSFORMER', $response['code']);
        $this->assertSame('Customer Name', $response['customer_name_snapshot']);
        $this->assertSame('2026-07-30', $response['delivery_date']);
        $this->assertSame('100.00', $response['subtotal']);
        $this->assertSame('110.00', $response['total_amount']);
        $this->assertSame('90.00', $response['remaining_amount']);
        $this->assertSame(1, $response['status']);
    }

    public function testOrderTransformerExposesCustomerAndItemsIncludes(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        /** @var Order $order */
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        OrderItem::factory()->create(['order_id' => $order->id]);
        $order->load(['customer', 'items']);
        $transformer = new OrderTransformer();

        $this->assertInstanceOf(Item::class, $transformer->includeCustomer($order));
        $this->assertInstanceOf(Collection::class, $transformer->includeItems($order));
    }

    public function testOrderItemTransformerReturnsSnapshotContract(): void
    {
        /** @var OrderItem $orderItem */
        $orderItem = OrderItem::factory()->create([
            'product_name_snapshot' => 'Snapshot Product',
            'product_price_snapshot' => '60.00',
            'unit_price' => '50.00',
            'price_override_reason' => 'Discount',
            'quantity' => 2,
            'total_item_price' => '100.00',
        ]);

        $response = (new OrderItemTransformer())->transform($orderItem);

        $this->assertSame('Snapshot Product', $response['product_name_snapshot']);
        $this->assertSame('60.00', $response['product_price_snapshot']);
        $this->assertSame('50.00', $response['unit_price']);
        $this->assertSame('Discount', $response['price_override_reason']);
        $this->assertSame(2, $response['quantity']);
        $this->assertSame('100.00', $response['total_item_price']);
        $this->assertSame($orderItem->encode($orderItem->product_id), $response['product_id']);
    }

    public function testOrderQueriesEagerLoadRequestedIncludes(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        /** @var Order $order */
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        OrderItem::factory()->create(['order_id' => $order->id]);
        request()->query->set('include', 'customer,items,items.product');

        /** @var Order $listedOrder */
        $listedOrder = app(GetAllOrdersTask::class)->run()->first();
        $foundOrder = app(FindOrderByIdTask::class)->run($order->id);

        $this->assertTrue($listedOrder->relationLoaded('customer'));
        $this->assertTrue($listedOrder->relationLoaded('items'));
        $this->assertTrue($listedOrder->items->first()->relationLoaded('product'));
        $this->assertTrue($foundOrder->relationLoaded('customer'));
        $this->assertTrue($foundOrder->relationLoaded('items'));
        $this->assertTrue($foundOrder->items->first()->relationLoaded('product'));
    }

    public function testGetAllOrdersTaskAppliesStableSorting(): void
    {
        Order::factory()->create(['created_at' => now()->subDay()]);
        Order::factory()->create(['created_at' => now()]);
        /** @var Order $newOrder2 */
        $newOrder2 = Order::factory()->create(['created_at' => now()]);

        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $orders */
        $orders = app(GetAllOrdersTask::class)->run();

        $this->assertGreaterThanOrEqual(3, $orders->count());
        /** @var Order $firstOrder */
        $firstOrder = $orders->first();
        $this->assertSame($newOrder2->id, $firstOrder->id);
    }
}
