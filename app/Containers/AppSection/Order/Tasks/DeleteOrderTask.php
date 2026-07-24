<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Order\Data\Repositories\OrderRepository;
use App\Containers\AppSection\Order\Models\Order;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteOrderTask extends ParentTask
{
    public function __construct(
        protected OrderRepository $repository
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function run($id, string $cancelReason): Order
    {
        try {
            /** @var Order $order */
            $order = $this->repository->find($id);

            return app(CancelOrderTask::class)->run($order, $cancelReason);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        } catch (Exception $e) {
            if ($e instanceof NotFoundException) {
                throw $e;
            }

            throw new NotFoundException();
        }
    }
}
