<?php

namespace App\Containers\AppSection\Customer\Actions;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Customer\Tasks\RestoreCustomerTask;
use App\Containers\AppSection\Customer\UI\API\Requests\RestoreCustomerRequest;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;

class RestoreCustomerAction extends ParentAction
{
    /**
     * @param RestoreCustomerRequest $request
     * @return Customer
     * @throws NotFoundException
     * @throws ValidationFailedException
     */
    public function run(RestoreCustomerRequest $request): Customer
    {
        return app(RestoreCustomerTask::class)->run($request->id);
    }
}
