<?php

/**
 * @apiGroup           Media
 * @apiName            ReorderProductMedia
 *
 * @api                {POST} /v1/media/reorder Reorder Product Media
 * @apiDescription     Sắp xếp lại thứ tự ảnh của sản phẩm (tối đa 9 items)
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {Object[]} medias Danh sách các object chứa id và sort_order mới
 * @apiParam           {String} medias.id Hashed ID của Media
 * @apiParam           {Number} medias.sort_order Thứ tự mới
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 202 Accepted
 * {
 *     "message": "Media order updated successfully."
 * }
 */

use App\Containers\AppSection\Media\UI\API\Controllers\ReorderProductMediaController;
use Illuminate\Support\Facades\Route;

Route::post('media/reorder', [ReorderProductMediaController::class, 'reorderMedia'])
    ->middleware(['auth:api']);
