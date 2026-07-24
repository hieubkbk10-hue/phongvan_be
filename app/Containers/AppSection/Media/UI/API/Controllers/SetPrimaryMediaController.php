<?php

namespace App\Containers\AppSection\Media\UI\API\Controllers;

use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Media\Actions\SetPrimaryMediaAction;
use App\Containers\AppSection\Media\UI\API\Requests\SetPrimaryMediaRequest;
use App\Containers\AppSection\Media\UI\API\Transformers\MediaTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class SetPrimaryMediaController extends ApiController
{
    /**
     * @param SetPrimaryMediaRequest $request
     * @return JsonResponse
     * @throws InvalidTransformerException
     */
    public function setPrimaryMedia(SetPrimaryMediaRequest $request): JsonResponse
    {
        $media = app(SetPrimaryMediaAction::class)->run($request);

        return $this->json($this->transform($media, MediaTransformer::class));
    }
}
