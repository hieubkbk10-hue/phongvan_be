<?php

namespace App\Containers\AppSection\Order\Tests\Unit;

use App\Containers\AppSection\Order\Models\OrderItem;
use App\Containers\AppSection\Order\Tests\TestCase;
use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tasks\DeleteProductTask;

class OrderItemProductDeletionTest extends TestCase
{
    public function testHardDeletingProductKeepsOrderItemSnapshot(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        /** @var OrderItem $orderItem */
        $orderItem = OrderItem::factory()->create([
            'product_id' => $product->id,
            'product_name_snapshot' => 'Sản phẩm lịch sử',
        ]);

        app(DeleteProductTask::class)->run($product->id);

        $orderItem->refresh();

        $this->assertDatabaseMissing(Product::getTableName(), ['id' => $product->id]);
        $this->assertNull($orderItem->product_id);
        $this->assertSame('Sản phẩm lịch sử', $orderItem->product_name_snapshot);
    }
}
