<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Order\Data\Repositories\OrderRepository;
use App\Containers\AppSection\Order\Models\Order;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Exception;

class FindOrderByIdTask extends ParentTask
{
    public function __construct(
        protected OrderRepository $repository
    ) {
    }

    /**
     * @throws NotFoundException
     *
     * @psalm-suppress UndefinedFunction
     */
    public function run($id): Order
    {
        try {
            $relations = [];

            if (\has_include('customer')) {
                $relations[] = 'customer';
            }

            if (\has_include('items')) {
                $relations[] = 'items';
            }

            if ($relations !== []) {
                $this->repository->with($relations);
            }

            return $this->repository->find($id);
        } catch (Exception) {
            throw new NotFoundException();
        }
    }
}
