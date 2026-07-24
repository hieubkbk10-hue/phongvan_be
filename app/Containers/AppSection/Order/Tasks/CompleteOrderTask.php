<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Order\Models\Order;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;

class CompleteOrderTask extends ParentTask
{
    /**
     * @param Order $order
     * @return Order
     * @throws ValidationFailedException
     */
    public function run(Order $order): Order
    {
        if ((int) $order->status !== Order::STATUS_PENDING) {
            throw (new ValidationFailedException('Only pending orders can be completed.'))
                ->withErrors(['status' => ['Only pending orders can be completed.']]);
        }

        if (empty($order->delivery_date) || empty($order->shipping_carrier)) {
            throw (new ValidationFailedException('Delivery date and shipping carrier are required before completing the order.'))
                ->withErrors(['order' => ['Delivery date and shipping carrier are required before completing the order.']]);
        }

        $order->update([
            'status' => Order::STATUS_COMPLETED,
        ]);

        /** @var Order $fresh */
        $fresh = $order->fresh();

        return $fresh;
    }
}
