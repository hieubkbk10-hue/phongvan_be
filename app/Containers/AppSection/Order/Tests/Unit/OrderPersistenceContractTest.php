<?php

namespace App\Containers\AppSection\Order\Tests\Unit;

use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Models\OrderItem;
use App\Containers\AppSection\Order\Tests\TestCase;
use App\Containers\AppSection\Product\Models\Product;
use Illuminate\Support\Facades\Schema;

/**
 * Class OrderPersistenceContractTest.
 *
 * @group order
 * @group unit
 */
class OrderPersistenceContractTest extends TestCase
{
    public function testOrderHasNoDeletedAtColumn(): void
    {
        $this->assertFalse(Schema::hasColumn('orders', 'deleted_at'));
    }

    public function testOrderConstantsAndCasts(): void
    {
        $this->assertEquals(1, Order::STATUS_PENDING);
        $this->assertEquals(2, Order::STATUS_COMPLETED);
        $this->assertEquals(5, Order::STATUS_CANCELLED);

        $this->assertEquals(1, Order::PAYMENT_METHOD_COD);
        $this->assertEquals(2, Order::PAYMENT_METHOD_CASH);
        $this->assertEquals(3, Order::PAYMENT_METHOD_BANK_TRANSFER);
        $this->assertEquals(4, Order::PAYMENT_METHOD_DEBT);

        /** @var Order $order */
        $order = Order::factory()->create([
            'subtotal' => 150.50,
            'shipping_fee' => 10.00,
            'total_amount' => 160.50,
            'advance_payment' => 50.00,
            'remaining_amount' => 110.50,
        ]);

        $this->assertSame('150.50', (string) $order->subtotal);
        $this->assertSame('10.00', (string) $order->shipping_fee);
        $this->assertSame('160.50', (string) $order->total_amount);
        $this->assertSame('50.00', (string) $order->advance_payment);
        $this->assertSame('110.50', (string) $order->remaining_amount);
    }

    public function testOrderItemFactoryAndSnapshots(): void
    {
        /** @var OrderItem $item */
        $item = OrderItem::factory()->create([
            'product_name_snapshot' => 'Super Phone',
            'product_price_snapshot' => 100.00,
            'unit_price' => 90.00,
            'price_override_reason' => 'VIP Discount',
            'quantity' => 2,
            'total_item_price' => 180.00,
        ]);

        $this->assertModelExists($item);
        $this->assertSame('100.00', (string) $item->product_price_snapshot);
        $this->assertSame('90.00', (string) $item->unit_price);
        $this->assertSame('VIP Discount', $item->price_override_reason);
        $this->assertSame('180.00', (string) $item->total_item_price);
    }

    public function testOrderItemProductHardDeleteSetsNull(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var OrderItem $item */
        $item = OrderItem::factory()->create([
            'product_id' => $product->id,
        ]);

        $product->forceDelete();

        $item->refresh();
        $this->assertNull($item->product_id);
        $this->assertModelExists($item);
    }

    public function testOrderDeletionCascadesOrderItems(): void
    {
        /** @var Order $order */
        $order = Order::factory()->create();

        /** @var OrderItem $item */
        $item = OrderItem::factory()->create([
            'order_id' => $order->id,
        ]);

        $order->delete();

        $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
    }
}
