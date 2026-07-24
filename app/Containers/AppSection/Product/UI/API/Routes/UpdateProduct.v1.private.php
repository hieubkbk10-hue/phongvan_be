<?php

/**
 * @apiGroup           Product
 * @apiName            UpdateProduct
 *
 * @api                {PATCH} /v1/products/:id Update Product
 * @apiDescription     Update an existing Product by ID.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} id Product Hash ID
 *
 * @apiBody            {String} [name] Product name (max 255)
 * @apiBody            {Number} [price] Product price (non-negative decimal)
 * @apiBody            {Number} [status] Product status (0=INACTIVE, 1=ACTIVE)
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

use App\Containers\AppSection\Product\UI\API\Controllers\UpdateProductController;
use Illuminate\Support\Facades\Route;

Route::patch('products/{id}', [UpdateProductController::class, 'updateProduct'])
    ->middleware(['auth:api']);
