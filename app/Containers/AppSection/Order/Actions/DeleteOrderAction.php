<?php

namespace App\Containers\AppSection\Order\Actions;

use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Tasks\DeleteOrderTask;
use App\Containers\AppSection\Order\Tasks\FindOrderForUpdateTask;
use App\Containers\AppSection\Order\UI\API\Requests\DeleteOrderRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

class DeleteOrderAction extends ParentAction
{
    public function run(DeleteOrderRequest $request): bool
    {
        $orderId = (int) $request->id;

        return DB::transaction(function () use ($orderId) {
            /** @var Order $order */
            $order = app(FindOrderForUpdateTask::class)->run($orderId);

            return app(DeleteOrderTask::class)->run($order);
        });
    }
}
