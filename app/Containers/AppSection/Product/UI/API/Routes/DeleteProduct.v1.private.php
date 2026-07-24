<?php

/**
 * @apiGroup           Product
 * @apiName            DeleteProduct
 *
 * @api                {DELETE} /v1/products/:id Delete Product
 * @apiDescription     Hard delete a Product by ID.
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
 * HTTP/1.1 204 No Content
 */

use App\Containers\AppSection\Product\UI\API\Controllers\DeleteProductController;
use Illuminate\Support\Facades\Route;

Route::delete('products/{id}', [DeleteProductController::class, 'deleteProduct'])
    ->middleware(['auth:api']);
