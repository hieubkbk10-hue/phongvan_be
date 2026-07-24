<?php

/**
 * @apiGroup           Customer
 * @apiName            GetAllCustomers
 *
 * @api                {GET} /v1/customers Get All Customers
 * @apiDescription     List Customers with pagination.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 200 OK
 * {
 *     "data": [
 *         {
 *             "object": "Customer",
 *             "id": "...",
 *             "name": "...",
 *             "phone": "+84901234567",
 *             "address": "...",
 *             "email": "..."
 *         }
 *     ],
 *     "meta": {
 *         "pagination": { ... }
 *     }
 * }
 */

use App\Containers\AppSection\Customer\UI\API\Controllers\GetAllCustomersController;
use Illuminate\Support\Facades\Route;

Route::get('customers', [GetAllCustomersController::class, 'getAllCustomers'])
    ->middleware(['auth:api']);
