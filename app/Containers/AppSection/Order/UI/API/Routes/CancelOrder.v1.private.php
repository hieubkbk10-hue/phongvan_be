<?php

/**
 * @apiGroup           Order
 * @apiName            CancelOrder
 *
 * @api                {POST} /v1/orders/:id/cancel Cancel Order
 * @apiDescription     Transition order status from Pending (1) to Cancelled (5). Requires cancel_reason body parameter. Cancelled orders cannot be modified or deleted.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} id Order Hashed ID
 * @apiBody            {String} cancel_reason Reason for cancelling order (max 255 chars)
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 200 OK
 * {
 *     "data": {
 *         "object": "Order",
 *         "id": "...",
 *         "status": 5,
 *         "cancel_reason": "Customer requested cancellation"
 *     }
 * }
 */

use App\Containers\AppSection\Order\UI\API\Controllers\CancelOrderController;
use Illuminate\Support\Facades\Route;

Route::post('orders/{id}/cancel', [CancelOrderController::class, 'cancelOrder'])
    ->middleware(['auth:api']);
