<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Order\Models\Order;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Tasks\Task as ParentTask;

class FindOrderForUpdateTask extends ParentTask
{
    /**
     * @param int $orderId
     * @return Order
     * @throws NotFoundException
     */
    public function run(int $orderId): Order
    {
        /** @var Order|null $order */
        $order = Order::where('id', $orderId)->lockForUpdate()->first();

        if (!$order) {
            throw new NotFoundException();
        }

        return $order;
    }
}
