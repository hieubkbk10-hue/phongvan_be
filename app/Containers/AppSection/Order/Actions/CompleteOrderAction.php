<?php

namespace App\Containers\AppSection\Order\Actions;

use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Tasks\CompleteOrderTask;
use App\Containers\AppSection\Order\Tasks\FindOrderForUpdateTask;
use App\Containers\AppSection\Order\Tasks\UpdateOrderTask;
use App\Containers\AppSection\Order\UI\API\Requests\CompleteOrderRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

class CompleteOrderAction extends ParentAction
{
    public function run(CompleteOrderRequest $request): Order
    {
        $sanitized = $request->sanitizeInput([
            'id',
            'delivery_date',
            'shipping_carrier',
        ]);

        $orderId = (int) $request->id;

        return DB::transaction(function () use ($orderId, $sanitized, $request) {
            /** @var Order $order */
            $order = app(FindOrderForUpdateTask::class)->run($orderId);

            // Optional update of shipping details if provided during complete
            $updates = [];
            if ($request->has('delivery_date') && !empty($sanitized['delivery_date'])) {
                $updates['delivery_date'] = $sanitized['delivery_date'];
            }
            if ($request->has('shipping_carrier') && !empty($sanitized['shipping_carrier'])) {
                $updates['shipping_carrier'] = $sanitized['shipping_carrier'];
            }

            if (!empty($updates)) {
                $order = app(UpdateOrderTask::class)->run($updates, $order->id);
            }

            return app(CompleteOrderTask::class)->run($order);
        });
    }
}
