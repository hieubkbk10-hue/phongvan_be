<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Order\Data\Repositories\OrderRepository;
use App\Containers\AppSection\Order\Models\Order;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Exception;

class DeleteOrderTask extends ParentTask
{
    public function __construct(
        protected OrderRepository $repository
    ) {
    }

    /**
     * @param Order $order
     * @return bool
     * @throws ValidationFailedException
     * @throws DeleteResourceFailedException
     */
    public function run(Order $order): bool
    {
        if ((int) $order->status !== Order::STATUS_PENDING) {
            throw (new ValidationFailedException('Only pending orders can be deleted.'))
                ->withErrors(['status' => ['Only pending orders can be deleted.']]);
        }

        if ((float) $order->advance_payment > 0.0) {
            throw (new ValidationFailedException('Orders with advance payment cannot be deleted.'))
                ->withErrors(['advance_payment' => ['Orders with advance payment cannot be deleted.']]);
        }

        try {
            return (bool) $order->delete();
        } catch (Exception) {
            throw new DeleteResourceFailedException();
        }
    }
}
