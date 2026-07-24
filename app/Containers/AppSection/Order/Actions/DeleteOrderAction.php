<?php

namespace App\Containers\AppSection\Order\Actions;

use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Tasks\DeleteOrderTask;
use App\Containers\AppSection\Order\UI\API\Requests\DeleteOrderRequest;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;

class DeleteOrderAction extends ParentAction
{
    /**
     * @param DeleteOrderRequest $request
     * @return Order
     * @throws NotFoundException
     * @throws UpdateResourceFailedException
     */
    public function run(DeleteOrderRequest $request): Order
    {
        $data = $request->sanitizeInput([
            'cancel_reason',
        ]);

        return app(DeleteOrderTask::class)->run($request->id, $data['cancel_reason']);
    }
}
