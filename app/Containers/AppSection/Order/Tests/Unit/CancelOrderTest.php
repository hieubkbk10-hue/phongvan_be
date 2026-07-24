<?php

namespace App\Containers\AppSection\Order\Tests\Unit;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Models\OrderItem;
use App\Containers\AppSection\Order\Tasks\DeleteOrderTask;
use App\Containers\AppSection\Order\Tests\TestCase;
use App\Containers\AppSection\Product\Models\Product;
use Illuminate\Support\Facades\Schema;

/**
 * @group order
 * @group unit
 */
class CancelOrderTest extends TestCase
{
    public function testOrdersTableDoesNotUseSoftDeletes(): void
    {
        $this->assertFalse(Schema::hasColumn(Order::getTableName(), 'deleted_at'));
    }

    public function testDeleteTaskCancelsOrderWithoutDeletingIt(): void
    {
        $order = Order::query()->create([
            'code' => 'ORD-CANCEL-TEST',
            'customer_name_snapshot' => 'Test Customer',
            'customer_phone_snapshot' => '0900000000',
            'customer_address_snapshot' => 'Test Address',
            'payment_method' => 1,
        ]);

        app(DeleteOrderTask::class)->run($order->id, 'Customer requested cancellation');

        $order->refresh();

        $this->assertSame(Order::STATUS_CANCELLED, $order->status);
        $this->assertSame('Customer requested cancellation', $order->cancel_reason);
        $this->assertDatabaseHas(Order::getTableName(), ['id' => $order->id]);
    }

    public function testOrderFactoryCreatesValidOrderWithRelations(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id]);

        $this->assertNotNull($order->id);
        $this->assertInstanceOf(Customer::class, $order->customer);
        $this->assertEquals($customer->id, $order->customer->id);
        $this->assertCount(1, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items->first());
        $this->assertInstanceOf(Order::class, $orderItem->order);
        $this->assertInstanceOf(Product::class, $orderItem->product);
    }
}
