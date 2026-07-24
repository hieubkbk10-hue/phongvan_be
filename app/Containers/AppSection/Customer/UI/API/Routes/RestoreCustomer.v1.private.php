<?php

/**
 * @apiGroup           Customer
 * @apiName            RestoreCustomer
 *
 * @api                {POST} /v1/customers/:id/restore Restore Customer
 * @apiDescription     Restore a soft-deleted Customer by ID.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} id Customer Hash ID
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 200 OK
 * {
 *     "data": {
 *         "object": "Customer",
 *         "id": "...",
 *         "name": "...",
 *         "phone": "+84901234567",
 *         "address": "...",
 *         "email": "..."
 *     }
 * }
 */

use App\Containers\AppSection\Customer\UI\API\Controllers\RestoreCustomerController;
use Illuminate\Support\Facades\Route;

Route::post('customers/{id}/restore', [RestoreCustomerController::class, 'restoreCustomer'])
    ->middleware(['auth:api']);
