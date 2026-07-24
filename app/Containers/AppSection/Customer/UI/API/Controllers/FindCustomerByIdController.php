<?php

namespace App\Containers\AppSection\Customer\UI\API\Controllers;

use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Customer\Actions\FindCustomerByIdAction;
use App\Containers\AppSection\Customer\UI\API\Requests\FindCustomerByIdRequest;
use App\Containers\AppSection\Customer\UI\API\Transformers\CustomerTransformer;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Controllers\ApiController;

class FindCustomerByIdController extends ApiController
{
    /**
     * @throws InvalidTransformerException|NotFoundException
     */
    public function findCustomerById(FindCustomerByIdRequest $request): array
    {
        $customer = app(FindCustomerByIdAction::class)->run($request);

        return $this->transform($customer, CustomerTransformer::class);
    }
}
