<?php

/**
 * @apiGroup           Order
 * @apiName            CompleteOrder
 *
 * @api                {POST} /v1/orders/:id/complete Complete Order
 * @apiDescription     Transition order status from Pending (1) to Completed (2). Order must have delivery_date and shipping_carrier specified. Completed orders cannot be modified or deleted.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} id Order Hashed ID
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 200 OK
 * {
 *     "data": {
 *         "object": "Order",
 *         "id": "...",
 *         "status": 2
 *     }
 * }
 */

use App\Containers\AppSection\Order\UI\API\Controllers\CompleteOrderController;
use Illuminate\Support\Facades\Route;

Route::post('orders/{id}/complete', [CompleteOrderController::class, 'completeOrder'])
    ->middleware(['auth:api']);
