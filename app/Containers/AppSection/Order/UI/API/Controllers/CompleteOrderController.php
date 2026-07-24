<?php

namespace App\Containers\AppSection\Order\UI\API\Controllers;

use App\Containers\AppSection\Order\Actions\CompleteOrderAction;
use App\Containers\AppSection\Order\UI\API\Requests\CompleteOrderRequest;
use App\Containers\AppSection\Order\UI\API\Transformers\OrderTransformer;
use App\Ship\Parents\Controllers\ApiController;

class CompleteOrderController extends ApiController
{
    public function completeOrder(CompleteOrderRequest $request): array
    {
        $order = app(CompleteOrderAction::class)->run($request);

        return $this->transform($order, OrderTransformer::class);
    }
}
