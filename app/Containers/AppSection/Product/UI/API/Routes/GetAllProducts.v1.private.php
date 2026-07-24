<?php

/**
 * @apiGroup           Product
 * @apiName            GetAllProducts
 *
 * @api                {GET} /v1/products Get All Products
 * @apiDescription     List Products with pagination and status filtering.
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
 *             "object": "Product",
 *             "id": "...",
 *             "name": "...",
 *             "price": "100.00",
 *             "status": 1
 *         }
 *     ],
 *     "meta": {
 *         "pagination": { ... }
 *     }
 * }
 */

use App\Containers\AppSection\Product\UI\API\Controllers\GetAllProductsController;
use Illuminate\Support\Facades\Route;

Route::get('products', [GetAllProductsController::class, 'getAllProducts'])
    ->middleware(['auth:api']);
