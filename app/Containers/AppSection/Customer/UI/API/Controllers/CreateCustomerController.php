<?php

namespace App\Containers\AppSection\Customer\UI\API\Controllers;

use Apiato\Core\Exceptions\IncorrectIdException;
use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Customer\Actions\CreateCustomerAction;
use App\Containers\AppSection\Customer\UI\API\Requests\CreateCustomerRequest;
use App\Containers\AppSection\Customer\UI\API\Transformers\CustomerTransformer;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class CreateCustomerController extends ApiController
{
    /**
     * @param CreateCustomerRequest $request
     * @return JsonResponse
     * @throws CreateResourceFailedException
     * @throws InvalidTransformerException
     * @throws IncorrectIdException
     */
    public function createCustomer(CreateCustomerRequest $request): JsonResponse
    {
        $customer = app(CreateCustomerAction::class)->run($request);

        return $this->created($this->transform($customer, CustomerTransformer::class));
    }
}
