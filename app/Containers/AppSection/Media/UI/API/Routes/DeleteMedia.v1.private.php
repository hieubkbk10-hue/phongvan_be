<?php

/**
 * @apiGroup           Media
 * @apiName            DeleteMedia
 *
 * @api                {DELETE} /v1/products/:product_id/media/:id Delete Media
 * @apiDescription     Xóa Media record và xóa file vật lý tương ứng khỏi storage
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} product_id Product Hash ID
 * @apiParam           {String} id Media Hash ID
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 204 No Content
 */

use App\Containers\AppSection\Media\UI\API\Controllers\DeleteMediaController;
use Illuminate\Support\Facades\Route;

Route::delete('products/{product_id}/media/{id}', [DeleteMediaController::class, 'deleteMedia'])
    ->middleware(['auth:api']);
