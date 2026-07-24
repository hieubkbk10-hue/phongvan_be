<?php

namespace App\Containers\AppSection\Order\UI\API\Controllers;

use Apiato\Core\Exceptions\IncorrectIdException;
use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Order\Actions\CreateOrderAction;
use App\Containers\AppSection\Order\UI\API\Requests\CreateOrderRequest;
use App\Containers\AppSection\Order\UI\API\Transformers\OrderTransformer;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class CreateOrderController extends ApiController
{
    /**
     * @param CreateOrderRequest $request
     * @return JsonResponse
     * @throws CreateResourceFailedException
     * @throws InvalidTransformerException
     * @throws IncorrectIdException
     */
    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        $order = app(CreateOrderAction::class)->run($request);

        return $this->created($this->transform($order, OrderTransformer::class));
    }
}
