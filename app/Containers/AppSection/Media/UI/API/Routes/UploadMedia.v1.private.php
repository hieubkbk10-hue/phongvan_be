<?php

/**
 * @apiGroup           Media
 * @apiName            UploadMedia
 *
 * @api                {POST} /v1/products/:product_id/media Upload Media File
 * @apiDescription     Upload file vật lý cho Product (tối đa 9 ảnh)
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} product_id Product Hash ID
 * @apiBody            {File} file File vật lý (required, max 10MB)
 * @apiBody            {Boolean} [is_main] Đặt làm ảnh chính
 * @apiBody            {Number} [sort_order] Thứ tự sắp xếp
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 201 Created
 * {
 *     "data": {
 *         "object": "Media",
 *         "id": "..."
 *     }
 * }
 */

use App\Containers\AppSection\Media\UI\API\Controllers\UploadMediaController;
use Illuminate\Support\Facades\Route;

Route::post('products/{product_id}/media', [UploadMediaController::class, 'uploadMedia'])
    ->middleware(['auth:api']);
