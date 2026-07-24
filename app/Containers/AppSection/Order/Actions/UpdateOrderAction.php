<?php

namespace App\Containers\AppSection\Order\Actions;

use Apiato\Core\Exceptions\IncorrectIdException;
use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Tasks\UpdateOrderTask;
use App\Containers\AppSection\Order\UI\API\Requests\UpdateOrderRequest;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;

class UpdateOrderAction extends ParentAction
{
    /**
     * @param UpdateOrderRequest $request
     * @return Order
     * @throws UpdateResourceFailedException
     * @throws IncorrectIdException
     * @throws NotFoundException
     */
    public function run(UpdateOrderRequest $request): Order
    {
        $data = $request->sanitizeInput([
            // add your request data here
        ]);

        return app(UpdateOrderTask::class)->run($data, $request->id);
    }
}
