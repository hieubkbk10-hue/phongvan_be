<?php

/**
 * @apiGroup           Media
 * @apiName            ReorderProductMedia
 *
 * @api                {POST} /v1/products/:product_id/media/reorder Reorder Product Media
 * @apiDescription     Sắp xếp lại thứ tự ảnh của sản phẩm (tối đa 9 items)
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} product_id Product Hash ID
 * @apiBody            {Object[]} media Danh sách các object chứa id và sort_order mới
 * @apiBody            {String} media.id Hashed ID của Media
 * @apiBody            {Number} media.sort_order Thứ tự mới
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 202 Accepted
 * {
 *     "message": "Media order updated successfully."
 * }
 */

use App\Containers\AppSection\Media\UI\API\Controllers\ReorderProductMediaController;
use Illuminate\Support\Facades\Route;

Route::post('products/{product_id}/media/reorder', [ReorderProductMediaController::class, 'reorderMedia'])
    ->middleware(['auth:api']);
