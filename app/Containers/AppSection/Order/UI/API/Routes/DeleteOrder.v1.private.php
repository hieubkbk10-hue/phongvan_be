<?php

/**
 * @apiGroup           Order
 * @apiName            DeleteOrder
 *
 * @api                {DELETE} /v1/orders/:id Delete Order
 * @apiDescription     Hard delete a Pending (1) Order with zero advance payment (advance_payment = 0). Order items will be cascade deleted. Completed, Cancelled, or orders with advance payments cannot be deleted.
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
 * HTTP/1.1 204 No Content
 */

use App\Containers\AppSection\Order\UI\API\Controllers\DeleteOrderController;
use Illuminate\Support\Facades\Route;

Route::delete('orders/{id}', [DeleteOrderController::class, 'deleteOrder'])
    ->middleware(['auth:api']);
