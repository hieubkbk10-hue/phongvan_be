<?php

namespace App\Containers\AppSection\Media\UI\API\Controllers;

use App\Containers\AppSection\Media\Actions\DeleteMediaAction;
use App\Containers\AppSection\Media\UI\API\Requests\DeleteMediaRequest;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Throwable;

class DeleteMediaController extends ApiController
{
    /**
     * @param DeleteMediaRequest $request
     * @return JsonResponse
     * @throws DeleteResourceFailedException
     * @throws NotFoundException
     * @throws Throwable
     */
    public function deleteMedia(DeleteMediaRequest $request): JsonResponse
    {
        app(DeleteMediaAction::class)->run($request);

        return $this->noContent();
    }
}
