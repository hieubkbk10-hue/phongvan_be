<?php

/**
 * @apiGroup           Customer
 * @apiName            DeleteCustomer
 *
 * @api                {DELETE} /v1/customers/:id Delete Customer
 * @apiDescription     Soft delete a Customer by ID.
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
 * HTTP/1.1 204 No Content
 */

use App\Containers\AppSection\Customer\UI\API\Controllers\DeleteCustomerController;
use Illuminate\Support\Facades\Route;

Route::delete('customers/{id}', [DeleteCustomerController::class, 'deleteCustomer'])
    ->middleware(['auth:api']);
