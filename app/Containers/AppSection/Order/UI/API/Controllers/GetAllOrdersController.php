<?php

namespace App\Containers\AppSection\Order\UI\API\Controllers;

use Apiato\Core\Exceptions\CoreInternalErrorException;
use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Order\Actions\GetAllOrdersAction;
use App\Containers\AppSection\Order\UI\API\Requests\GetAllOrdersRequest;
use App\Containers\AppSection\Order\UI\API\Transformers\OrderTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Prettus\Repository\Exceptions\RepositoryException;

class GetAllOrdersController extends ApiController
{
    /**
     * @throws InvalidTransformerException
     * @throws CoreInternalErrorException
     * @throws RepositoryException
     */
    public function getAllOrders(GetAllOrdersRequest $request): array
    {
        $orders = app(GetAllOrdersAction::class)->run($request);

        return $this->transform($orders, OrderTransformer::class);
    }
}
