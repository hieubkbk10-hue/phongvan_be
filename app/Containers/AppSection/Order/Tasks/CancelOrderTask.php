<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Order\Models\Order;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;

class CancelOrderTask extends ParentTask
{
    /**
     * @param Order $order
     * @param string $reason
     * @return Order
     * @throws ValidationFailedException
     */
    public function run(Order $order, string $reason): Order
    {
        if ((int) $order->status !== Order::STATUS_PENDING) {
            throw (new ValidationFailedException('Only pending orders can be cancelled.'))
                ->withErrors(['status' => ['Only pending orders can be cancelled.']]);
        }

        if (empty(trim($reason))) {
            throw (new ValidationFailedException('Cancellation reason is required.'))
                ->withErrors(['reason' => ['Cancellation reason is required.']]);
        }

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'cancel_reason' => trim($reason),
        ]);

        /** @var Order $fresh */
        $fresh = $order->fresh();

        return $fresh;
    }
}
