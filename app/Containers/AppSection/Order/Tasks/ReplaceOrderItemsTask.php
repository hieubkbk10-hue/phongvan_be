<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Order\Models\OrderItem;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;

class ReplaceOrderItemsTask extends ParentTask
{
    /**
     * @param int $orderId
     * @param array $processedItems
     * @return void
     * @throws CreateResourceFailedException
     */
    public function run(int $orderId, array $processedItems): void
    {
        // Delete existing items for this order
        OrderItem::where('order_id', $orderId)->delete();

        // Create new items
        app(CreateOrderItemsTask::class)->run($orderId, $processedItems);
    }
}
