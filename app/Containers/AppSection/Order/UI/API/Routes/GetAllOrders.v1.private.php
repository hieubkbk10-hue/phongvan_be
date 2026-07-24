<?php

/**
 * @apiGroup           Order
 * @apiName            GetAllOrders
 *
 * @api                {GET} /v1/orders Get All Orders
 * @apiDescription     Get paginated list of Orders sorted by created_at DESC, id DESC. Supports filtering by status, payment_method, and relation includes.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiQuery           {Number} [page] Page number
 * @apiQuery           {Number} [limit] Page size limit (max 100)
 * @apiQuery           {Number} [status] Filter by status (1=Pending, 2=Completed, 5=Cancelled)
 * @apiQuery           {Number} [payment_method] Filter by payment_method (1=COD, 2=CASH, 3=BANK_TRANSFER, 4=DEBT)
 * @apiQuery           {String} [include] Include relations: customer, items, items.product
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 200 OK
 * {
 *     "data": [
 *         {
 *             "object": "Order",
 *             "id": "...",
 *             "code": "ORD-...",
 *             "status": 1
 *         }
 *     ],
 *     "meta": {
 *         "pagination": { ... }
 *     }
 * }
 */

use App\Containers\AppSection\Order\UI\API\Controllers\GetAllOrdersController;
use Illuminate\Support\Facades\Route;

Route::get('orders', [GetAllOrdersController::class, 'getAllOrders'])
    ->middleware(['auth:api']);
