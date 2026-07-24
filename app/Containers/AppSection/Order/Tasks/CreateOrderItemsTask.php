<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Order\Data\Repositories\OrderItemRepository;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Exception;

class CreateOrderItemsTask extends ParentTask
{
    public function __construct(
        protected OrderItemRepository $orderItemRepository
    ) {
    }

    /**
     * @param int $orderId
     * @param array $processedItems
     * @return void
     * @throws CreateResourceFailedException
     */
    public function run(int $orderId, array $processedItems): void
    {
        try {
            foreach ($processedItems as $item) {
                $item['order_id'] = $orderId;
                $this->orderItemRepository->create($item);
            }
        } catch (Exception $e) {
            throw new CreateResourceFailedException($e->getMessage());
        }
    }
}
