<?php

/**
 * @apiGroup           Media
 * @apiName            UploadMedia
 *
 * @api                {POST} /v1/media/upload Upload Media File
 * @apiDescription     Upload file vật lý và tạo Media record
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {File} file File vật lý (required)
 * @apiParam           {String} [mediable_type] Loại đối tượng sở hữu
 * @apiParam           {String} [mediable_id] ID đối tượng sở hữu
 * @apiParam           {Boolean} [is_main] Đặt làm ảnh chính
 * @apiParam           {Number} [sort_order] Thứ tự sắp xếp
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

Route::post('media/upload', [UploadMediaController::class, 'uploadMedia'])
    ->middleware(['auth:api']);
