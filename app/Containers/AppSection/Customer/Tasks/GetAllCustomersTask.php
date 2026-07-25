<?php

namespace App\Containers\AppSection\Customer\Tasks;

use Apiato\Core\Exceptions\CoreInternalErrorException;
use App\Containers\AppSection\Customer\Data\Repositories\CustomerRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Prettus\Repository\Exceptions\RepositoryException;

class GetAllCustomersTask extends ParentTask
{
    public function __construct(
        protected CustomerRepository $repository
    ) {
    }

    /**
     * @throws CoreInternalErrorException
     * @throws RepositoryException
     */
    public function run(): mixed
    {
        $trashed = request('trashed');
        if ($trashed === 'only') {
            $this->repository->scopeQuery(fn ($query) => $query->onlyTrashed());
        } elseif ($trashed === 'with') {
            $this->repository->scopeQuery(fn ($query) => $query->withTrashed());
        }

        return $this->addRequestCriteria()
            ->repository
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate();
    }
}
