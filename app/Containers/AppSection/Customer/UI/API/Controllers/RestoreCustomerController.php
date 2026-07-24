<?php

namespace App\Containers\AppSection\Customer\UI\API\Controllers;

use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Customer\Actions\RestoreCustomerAction;
use App\Containers\AppSection\Customer\UI\API\Requests\RestoreCustomerRequest;
use App\Containers\AppSection\Customer\UI\API\Transformers\CustomerTransformer;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Controllers\ApiController;

class RestoreCustomerController extends ApiController
{
    /**
     * @param RestoreCustomerRequest $request
     * @return array
     * @throws InvalidTransformerException
     * @throws NotFoundException
     * @throws ValidationFailedException
     */
    public function restoreCustomer(RestoreCustomerRequest $request): array
    {
        $customer = app(RestoreCustomerAction::class)->run($request);

        return $this->transform($customer, CustomerTransformer::class);
    }
}
