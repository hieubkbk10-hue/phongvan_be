<?php

namespace App\Containers\AppSection\Media\UI\API\Controllers;

use App\Containers\AppSection\Media\Actions\ReorderProductMediaAction;
use App\Containers\AppSection\Media\UI\API\Requests\ReorderMediaRequest;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class ReorderProductMediaController extends ApiController
{
    /**
     * @param ReorderMediaRequest $request
     * @return JsonResponse
     */
    public function reorderMedia(ReorderMediaRequest $request): JsonResponse
    {
        app(ReorderProductMediaAction::class)->run($request);

        return $this->accepted(['message' => 'Media order updated successfully.']);
    }
}
