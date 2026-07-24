<?php

/**
 * @apiGroup           Customer
 * @apiName            UpdateCustomer
 *
 * @api                {PATCH} /v1/customers/:id Update Customer
 * @apiDescription     Update an existing Customer by ID.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} id Customer Hash ID
 *
 * @apiBody            {String} [name] Max 150 chars
 * @apiBody            {String} [phone] E.164 format (e.g. +84901234567)
 * @apiBody            {String} [address] Max 255 chars
 * @apiBody            {String} [email] Max 150 chars
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

use App\Containers\AppSection\Customer\UI\API\Controllers\UpdateCustomerController;
use Illuminate\Support\Facades\Route;

Route::patch('customers/{id}', [UpdateCustomerController::class, 'updateCustomer'])
    ->middleware(['auth:api']);
