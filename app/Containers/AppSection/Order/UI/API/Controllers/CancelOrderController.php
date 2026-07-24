<?php

namespace App\Containers\AppSection\Order\UI\API\Controllers;

use App\Containers\AppSection\Order\Actions\CancelOrderAction;
use App\Containers\AppSection\Order\UI\API\Requests\CancelOrderRequest;
use App\Containers\AppSection\Order\UI\API\Transformers\OrderTransformer;
use App\Ship\Parents\Controllers\ApiController;

class CancelOrderController extends ApiController
{
    public function cancelOrder(CancelOrderRequest $request): array
    {
        $order = app(CancelOrderAction::class)->run($request);

        return $this->transform($order, OrderTransformer::class);
    }
}
