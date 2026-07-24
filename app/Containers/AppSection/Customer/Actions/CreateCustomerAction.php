<?php

namespace App\Containers\AppSection\Customer\Actions;

use Apiato\Core\Exceptions\IncorrectIdException;
use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Customer\Tasks\CreateCustomerTask;
use App\Containers\AppSection\Customer\UI\API\Requests\CreateCustomerRequest;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;

class CreateCustomerAction extends ParentAction
{
    /**
     * @param CreateCustomerRequest $request
     * @return Customer
     * @throws CreateResourceFailedException
     * @throws IncorrectIdException
     */
    public function run(CreateCustomerRequest $request): Customer
    {
        $data = $request->sanitizeInput([
            // add your request data here
        ]);

        return app(CreateCustomerTask::class)->run($data);
    }
}
