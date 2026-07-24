<?php

/**
 * @apiGroup           Product
 * @apiName            CreateProduct
 *
 * @api                {POST} /v1/products Create Product
 * @apiDescription     Create a new Product.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiBody            {String} name Product name (max 255)
 * @apiBody            {Number} price Product price (non-negative decimal)
 * @apiBody            {Number} [status=1] Product status (0=INACTIVE, 1=ACTIVE)
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 201 Created
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

use App\Containers\AppSection\Product\UI\API\Controllers\CreateProductController;
use Illuminate\Support\Facades\Route;

Route::post('products', [CreateProductController::class, 'createProduct'])
    ->middleware(['auth:api']);
