<?php

/**
 * @apiGroup           Order
 * @apiName            DeleteOrder
 *
 * @api                {DELETE} /v1/orders/:id Cancel Order
 * @apiDescription     Chuyển đơn hàng sang trạng thái đã hủy và giữ lại dữ liệu lịch sử.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} id ID của đơn hàng.
 * @apiBody            {String{1..255}} cancel_reason Lý do hủy đơn hàng.
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 204 No Content
 */

use App\Containers\AppSection\Order\UI\API\Controllers\DeleteOrderController;
use Illuminate\Support\Facades\Route;

Route::delete('orders/{id}', [DeleteOrderController::class, 'deleteOrder'])
    ->middleware(['auth:api']);
