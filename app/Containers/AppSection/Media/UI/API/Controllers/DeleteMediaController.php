<?php

namespace App\Containers\AppSection\Media\UI\API\Controllers;

use App\Containers\AppSection\Media\Actions\DeleteMediaAction;
use App\Containers\AppSection\Media\UI\API\Requests\DeleteMediaRequest;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class DeleteMediaController extends ApiController
{
    /**
     * @param DeleteMediaRequest $request
     * @return JsonResponse
     */
    public function deleteMedia(DeleteMediaRequest $request): JsonResponse
    {
        app(DeleteMediaAction::class)->run($request);

        return $this->noContent();
    }
}
