<?php

namespace App\Containers\AppSection\Order\Actions;

use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Tasks\CancelOrderTask;
use App\Containers\AppSection\Order\Tasks\FindOrderForUpdateTask;
use App\Containers\AppSection\Order\UI\API\Requests\CancelOrderRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

class CancelOrderAction extends ParentAction
{
    public function run(CancelOrderRequest $request): Order
    {
        $sanitized = $request->sanitizeInput([
            'id',
            'reason',
            'cancel_reason',
        ]);

        $orderId = (int) $request->id;
        $reason = $sanitized['cancel_reason'] ?? ($sanitized['reason'] ?? '');

        return DB::transaction(function () use ($orderId, $reason) {
            /** @var Order $order */
            $order = app(FindOrderForUpdateTask::class)->run($orderId);

            return app(CancelOrderTask::class)->run($order, $reason);
        });
    }
}
