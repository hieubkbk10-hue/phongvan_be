<?php

namespace App\Containers\AppSection\Product\UI\API\Tests\Functional;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Models\OrderItem;
use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\UI\API\Tests\ApiTestCase;
use Illuminate\Support\Facades\Storage;

/**
 * Class DeleteProductTest.
 *
 * @group product
 * @group api
 */
class DeleteProductTest extends ApiTestCase
{
    protected string $endpoint = 'delete@v1/products/{id}';

    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function testDeleteProductHardDeletesRecord(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $response = $this->injectId($product->id)->makeCall();

        $response->assertStatus(204);

        // Record must be completely removed from database (hard delete)
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function testDeleteProductCleansUpAssociatedMediaAndFiles(): void
    {
        Storage::fake('public');

        /** @var Product $product */
        $product = Product::factory()->create();

        $path = 'products/test_image.jpg';
        Storage::disk('public')->put($path, 'fake image content');

        /** @var Media $media */
        $media = Media::create([
            'disk' => 'public',
            'path' => $path,
            'filename' => 'test_image.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'sort_order' => 1,
            'is_main' => true,
            'mediable_type' => Product::class,
            'mediable_id' => $product->id,
        ]);

        $response = $this->injectId($product->id)->makeCall();

        $response->assertStatus(204);

        // Product database record missing
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);

        // Media database record missing
        $this->assertDatabaseMissing('media', [
            'id' => $media->id,
        ]);

        // Physical file deleted from storage
        Storage::disk('public')->assertMissing($path);
    }

    public function testDeleteProductSetsAssociatedOrderItemProductIdToNull(): void
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create([
            'name' => 'Sample Product for Order',
            'price' => 500.00,
        ]);

        /** @var Order $order */
        $order = Order::create([
            'code' => 'ORD-TEST-001',
            'customer_id' => $customer->id,
            'customer_name_snapshot' => $customer->name,
            'customer_phone_snapshot' => $customer->phone,
            'customer_address_snapshot' => $customer->address,
            'delivery_date' => '2026-08-01',
            'shipping_carrier' => 'GHTK',
            'payment_method' => 1,
            'subtotal' => 500.00,
            'shipping_fee' => 0.00,
            'total_amount' => 500.00,
            'advance_payment' => 0.00,
            'remaining_amount' => 500.00,
            'status' => 1,
        ]);

        /** @var OrderItem $item */
        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'unit_price' => 500.00,
            'quantity' => 1,
            'total_item_price' => 500.00,
        ]);

        $response = $this->injectId($product->id)->makeCall();

        $response->assertStatus(204);

        // Product database record missing
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);

        // OrderItem database record still exists, but product_id is null
        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'product_id' => null,
            'product_name_snapshot' => 'Sample Product for Order',
            'unit_price' => 500.00,
        ]);
    }

    public function testDeleteNonExistingProduct(): void
    {
        $invalidId = 999999;

        $response = $this->injectId($invalidId)->makeCall();

        $response->assertStatus(422);
    }
}
