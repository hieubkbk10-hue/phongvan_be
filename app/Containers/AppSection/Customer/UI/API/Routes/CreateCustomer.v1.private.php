<?php

/**
 * @apiGroup           Customer
 * @apiName            CreateCustomer
 *
 * @api                {POST} /v1/customers Create Customer
 * @apiDescription     Create a new Customer.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiBody            {String} name Max 150 chars
 * @apiBody            {String} phone E.164 format (e.g. +84901234567)
 * @apiBody            {String} address Max 255 chars
 * @apiBody            {String} [email] Max 150 chars
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 201 Created
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

use App\Containers\AppSection\Customer\UI\API\Controllers\CreateCustomerController;
use Illuminate\Support\Facades\Route;

Route::post('customers', [CreateCustomerController::class, 'createCustomer'])
    ->middleware(['auth:api']);
