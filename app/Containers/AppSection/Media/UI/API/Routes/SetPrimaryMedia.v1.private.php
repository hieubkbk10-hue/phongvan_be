<?php

/**
 * @apiGroup           Media
 * @apiName            SetPrimaryMedia
 *
 * @api                {PATCH} /v1/media/:id/primary Set Primary Media
 * @apiDescription     Đặt 1 Media làm ảnh chính cho sản phẩm/đối tượng sở hữu
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
 *     "data": {
 *         "object": "Media",
 *         "id": "...",
 *         "is_primary": true
 *     }
 * }
 */

use App\Containers\AppSection\Media\UI\API\Controllers\SetPrimaryMediaController;
use Illuminate\Support\Facades\Route;

Route::patch('media/{id}/primary', [SetPrimaryMediaController::class, 'setPrimaryMedia'])
    ->middleware(['auth:api']);
