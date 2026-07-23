<?php

namespace App\Containers\AppSection\Media\UI\API\Controllers;

use App\Containers\AppSection\Media\Actions\ReorderProductMediaAction;
use App\Containers\AppSection\Media\UI\API\Requests\ReorderMediaRequest;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Throwable;

class ReorderProductMediaController extends ApiController
{
    /**
     * @param ReorderMediaRequest $request
     * @return JsonResponse
     * @throws UpdateResourceFailedException
     * @throws Throwable
     */
    public function reorderMedia(ReorderMediaRequest $request): JsonResponse
    {
        /** @var array<int, array{id: int, sort_order: int}> $medias */
        $medias = $request->input('medias', []);
        app(ReorderProductMediaAction::class)->run($medias);

        return $this->accepted([
            'message' => 'Media order updated successfully.',
        ]);
    }
}
