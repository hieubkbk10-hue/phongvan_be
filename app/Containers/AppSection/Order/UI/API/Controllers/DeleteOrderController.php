<?php

namespace App\Containers\AppSection\Order\UI\API\Controllers;

use App\Containers\AppSection\Order\Actions\DeleteOrderAction;
use App\Containers\AppSection\Order\UI\API\Requests\DeleteOrderRequest;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class DeleteOrderController extends ApiController
{
    /**
     * @param DeleteOrderRequest $request
     * @return JsonResponse
     * @throws NotFoundException
     * @throws UpdateResourceFailedException
     */
    public function deleteOrder(DeleteOrderRequest $request): JsonResponse
    {
        app(DeleteOrderAction::class)->run($request);

        return $this->noContent();
    }
}
