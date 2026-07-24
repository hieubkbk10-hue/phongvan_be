<?php

namespace App\Containers\AppSection\Order\Tasks;

use Apiato\Core\Exceptions\CoreInternalErrorException;
use App\Containers\AppSection\Order\Data\Repositories\OrderRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Prettus\Repository\Exceptions\RepositoryException;

class GetAllOrdersTask extends ParentTask
{
    public function __construct(
        protected OrderRepository $repository
    ) {
    }

    /**
     * @throws CoreInternalErrorException
     * @throws RepositoryException
     *
     * @psalm-suppress UndefinedFunction
     */
    public function run(): mixed
    {
        $repository = $this->addRequestCriteria()->repository;
        $relations = [];

        if (\has_include('customer')) {
            $relations[] = 'customer';
        }

        if (\has_include('items')) {
            $relations[] = 'items';
        }

        if ($relations !== []) {
            $repository->with($relations);
        }

        return $repository->paginate();
    }
}
