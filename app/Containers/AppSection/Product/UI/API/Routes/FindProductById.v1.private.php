<?php

/**
 * @apiGroup           Product
 * @apiName            FindProductById
 *
 * @api                {GET} /v1/products/:id Find Product By Id
 * @apiDescription     Get a Product by ID.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} id Product Hash ID
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 200 OK
 * {
 *     "data": {
 *         "object": "Product",
 *         "id": "...",
 *         "name": "...",
 *         "price": "100.00",
 *         "status": 1
 *     }
 * }
 */

use App\Containers\AppSection\Product\UI\API\Controllers\FindProductByIdController;
use Illuminate\Support\Facades\Route;

Route::get('products/{id}', [FindProductByIdController::class, 'findProductById'])
    ->middleware(['auth:api']);
