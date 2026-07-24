<?php

namespace App\Containers\AppSection\Order\Tests\Unit;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Order\Actions\CancelOrderAction;
use App\Containers\AppSection\Order\Actions\CompleteOrderAction;
use App\Containers\AppSection\Order\Actions\CreateOrderAction;
use App\Containers\AppSection\Order\Actions\DeleteOrderAction;
use App\Containers\AppSection\Order\Actions\UpdateOrderAction;
use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Models\OrderItem;
use App\Containers\AppSection\Order\Tests\TestCase;
use App\Containers\AppSection\Order\UI\API\Requests\CancelOrderRequest;
use App\Containers\AppSection\Order\UI\API\Requests\CompleteOrderRequest;
use App\Containers\AppSection\Order\UI\API\Requests\CreateOrderRequest;
use App\Containers\AppSection\Order\UI\API\Requests\DeleteOrderRequest;
use App\Containers\AppSection\Order\UI\API\Requests\UpdateOrderRequest;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Exceptions\ValidationFailedException;

/**
 * Class TransactionalOrderActionsTest.
 *
 * @group order
 * @group unit
 */
class TransactionalOrderActionsTest extends TestCase
{
    public function testCreateOrderActionCreatesOrderAndCustomerAndItemsTransactionally(): void
    {
        $uniquePhone = '+8490' . rand(1000000, 9999999);

        /** @var Product $product */
        $product = Product::factory()->create([
            'price' => '200.00',
            'status' => Product::STATUS_ACTIVE,
        ]);

        $request = new CreateOrderRequest();
        $request->replace([
            'customer_name_snapshot' => 'Alice Wonder',
            'customer_phone_snapshot' => $uniquePhone,
            'customer_address_snapshot' => '777 Wonderland St',
            'payment_method' => Order::PAYMENT_COD,
            'shipping_fee' => '20.00',
            'advance_payment' => '50.00',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        /** @var Order $order */
        $order = app(CreateOrderAction::class)->run($request);

        $this->assertNotNull($order->id);
        $this->assertStringStartsWith('ORD-', $order->code);
        $this->assertEquals('Alice Wonder', $order->customer_name_snapshot);
        $this->assertEquals($uniquePhone, $order->customer_phone_snapshot);
        $this->assertEquals('600.00', $order->subtotal);
        $this->assertEquals('20.00', $order->shipping_fee);
        $this->assertEquals('620.00', $order->total_amount);
        $this->assertEquals('50.00', $order->advance_payment);
        $this->assertEquals('570.00', $order->remaining_amount);
        $this->assertEquals(Order::STATUS_PENDING, (int) $order->status);

        $this->assertCount(1, $order->items);
        $this->assertEquals($product->id, $order->items[0]->product_id);
        $this->assertEquals('600.00', $order->items[0]->total_item_price);

        // Verify Customer was persisted
        $this->assertDatabaseHas('customers', ['phone' => $uniquePhone]);
    }

    public function testCreateOrderActionRollsBackOnFailure(): void
    {
        $uniquePhone = '+8491' . rand(1000000, 9999999);

        /** @var Product $product */
        $product = Product::factory()->create([
            'price' => '100.00',
            'status' => Product::STATUS_ACTIVE,
        ]);

        $request = new CreateOrderRequest();
        $request->replace([
            'customer_name_snapshot' => 'Bob Builder',
            'customer_phone_snapshot' => $uniquePhone,
            'customer_address_snapshot' => '999 Construction Ave',
            'payment_method' => Order::PAYMENT_COD,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => 999999, // invalid ID to trigger exception inside transaction
                    'quantity' => 1,
                ],
            ],
        ]);

        try {
            app(CreateOrderAction::class)->run($request);
            $this->fail('Expected ValidationFailedException was not thrown.');
        } catch (ValidationFailedException) {
            // Expected exception
        }

        // Verify Customer and Order were rolled back and DO NOT exist in database
        $this->assertDatabaseMissing('customers', ['phone' => $uniquePhone]);
        $this->assertDatabaseMissing('orders', ['customer_phone_snapshot' => $uniquePhone]);
    }

    public function testUpdateOrderActionUpdatesPendingOrder(): void
    {
        $uniquePhone = '+8492' . rand(1000000, 9999999);
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['phone' => $uniquePhone]);
        /** @var Product $product1 */
        $product1 = Product::factory()->create(['price' => '100.00', 'status' => Product::STATUS_ACTIVE]);
        /** @var Product $product2 */
        $product2 = Product::factory()->create(['price' => '150.00', 'status' => Product::STATUS_ACTIVE]);

        /** @var Order $order */
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'status' => Order::STATUS_PENDING,
            'subtotal' => '100.00',
            'total_amount' => '100.00',
            'remaining_amount' => '100.00',
        ]);

        $request = new UpdateOrderRequest();
        $request->replace([
            'id' => $order->id,
            'shipping_fee' => '30.00',
            'items' => [
                [
                    'product_id' => $product2->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        /** @var Order $updated */
        $updated = app(UpdateOrderAction::class)->run($request);

        $this->assertEquals('300.00', $updated->subtotal);
        $this->assertEquals('30.00', $updated->shipping_fee);
        $this->assertEquals('330.00', $updated->total_amount);
        $this->assertEquals('330.00', $updated->remaining_amount);
        $this->assertCount(1, $updated->items);
        $this->assertEquals($product2->id, $updated->items[0]->product_id);
    }

    public function testUpdateOrderActionRejectsCompletedOrCancelledOrder(): void
    {
        $uniquePhone = '+8493' . rand(1000000, 9999999);
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['phone' => $uniquePhone]);

        /** @var Order $completedOrder */
        $completedOrder = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'status' => Order::STATUS_COMPLETED,
            'delivery_date' => '2026-07-25',
            'shipping_carrier' => 'FedEx',
        ]);

        $req1 = new UpdateOrderRequest();
        $req1->replace([
            'id' => $completedOrder->id,
            'shipping_fee' => '10.00',
        ]);

        $this->expectException(ValidationFailedException::class);
        app(UpdateOrderAction::class)->run($req1);
    }

    public function testUpdateOrderActionRejectsCancelledOrder(): void
    {
        $uniquePhone = '+8494' . rand(1000000, 9999999);
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['phone' => $uniquePhone]);

        /** @var Order $cancelledOrder */
        $cancelledOrder = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'status' => Order::STATUS_CANCELLED,
            'cancel_reason' => 'Out of stock',
        ]);

        $req2 = new UpdateOrderRequest();
        $req2->replace([
            'id' => $cancelledOrder->id,
            'shipping_fee' => '10.00',
        ]);

        $this->expectException(ValidationFailedException::class);
        app(UpdateOrderAction::class)->run($req2);
    }

    public function testCompleteOrderActionTransitionsPendingToCompleted(): void
    {
        $uniquePhone = '+8495' . rand(1000000, 9999999);
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['phone' => $uniquePhone]);

        /** @var Order $order */
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'status' => Order::STATUS_PENDING,
            'delivery_date' => '2026-07-25',
            'shipping_carrier' => 'GHN',
        ]);

        $request = new CompleteOrderRequest();
        $request->replace(['id' => $order->id]);

        /** @var Order $completed */
        $completed = app(CompleteOrderAction::class)->run($request);

        $this->assertEquals(Order::STATUS_COMPLETED, (int) $completed->status);
    }

    public function testCompleteOrderActionFailsIfShippingDetailsMissing(): void
    {
        $uniquePhone = '+8496' . rand(1000000, 9999999);
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['phone' => $uniquePhone]);

        /** @var Order $order */
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'status' => Order::STATUS_PENDING,
            'delivery_date' => null,
            'shipping_carrier' => null,
        ]);

        $request = new CompleteOrderRequest();
        $request->replace(['id' => $order->id]);

        $this->expectException(ValidationFailedException::class);
        app(CompleteOrderAction::class)->run($request);
    }

    public function testCancelOrderActionTransitionsPendingToCancelledWithReason(): void
    {
        $uniquePhone = '+8497' . rand(1000000, 9999999);
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['phone' => $uniquePhone]);

        /** @var Order $order */
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'status' => Order::STATUS_PENDING,
        ]);

        $request = new CancelOrderRequest();
        $request->replace([
            'id' => $order->id,
            'reason' => 'Customer changed mind',
        ]);

        /** @var Order $cancelled */
        $cancelled = app(CancelOrderAction::class)->run($request);

        $this->assertEquals(Order::STATUS_CANCELLED, (int) $cancelled->status);
        $this->assertEquals('Customer changed mind', $cancelled->cancel_reason);
    }

    public function testCancelOrderActionFailsWithoutReason(): void
    {
        $uniquePhone = '+8498' . rand(1000000, 9999999);
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['phone' => $uniquePhone]);

        /** @var Order $order */
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'status' => Order::STATUS_PENDING,
        ]);

        $request = new CancelOrderRequest();
        $request->replace([
            'id' => $order->id,
            'reason' => '',
        ]);

        $this->expectException(ValidationFailedException::class);
        app(CancelOrderAction::class)->run($request);
    }

    public function testDeleteOrderActionHardDeletesPendingOrderWithZeroAdvance(): void
    {
        $uniquePhone = '+8499' . rand(1000000, 9999999);
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['phone' => $uniquePhone]);
        /** @var Product $product */
        $product = Product::factory()->create(['status' => Product::STATUS_ACTIVE]);

        /** @var Order $order */
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'status' => Order::STATUS_PENDING,
            'advance_payment' => '0.00',
        ]);

        /** @var OrderItem $item */
        $item = OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $request = new DeleteOrderRequest();
        $request->replace(['id' => $order->id]);

        $result = app(DeleteOrderAction::class)->run($request);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function testDeleteOrderActionFailsForPendingOrderWithAdvancePayment(): void
    {
        $uniquePhone = '+8489' . rand(1000000, 9999999);
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['phone' => $uniquePhone]);

        /** @var Order $order */
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'status' => Order::STATUS_PENDING,
            'advance_payment' => '50.00',
        ]);

        $request = new DeleteOrderRequest();
        $request->replace(['id' => $order->id]);

        $this->expectException(ValidationFailedException::class);
        app(DeleteOrderAction::class)->run($request);
    }

    public function testDeleteOrderActionFailsForCompletedOrCancelledOrder(): void
    {
        $uniquePhone = '+8488' . rand(1000000, 9999999);
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['phone' => $uniquePhone]);

        /** @var Order $completedOrder */
        $completedOrder = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'status' => Order::STATUS_COMPLETED,
            'advance_payment' => '0.00',
        ]);

        $request = new DeleteOrderRequest();
        $request->replace(['id' => $completedOrder->id]);

        $this->expectException(ValidationFailedException::class);
        app(DeleteOrderAction::class)->run($request);
    }
}
