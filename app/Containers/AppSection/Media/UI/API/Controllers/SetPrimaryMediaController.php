<?php

namespace App\Containers\AppSection\Media\UI\API\Controllers;

use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Media\Actions\SetPrimaryMediaAction;
use App\Containers\AppSection\Media\UI\API\Requests\SetPrimaryMediaRequest;
use App\Containers\AppSection\Media\UI\API\Transformers\MediaTransformer;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Controllers\ApiController;
use Throwable;

class SetPrimaryMediaController extends ApiController
{
    /**
     * @param SetPrimaryMediaRequest $request
     * @return array
     * @throws NotFoundException
     * @throws UpdateResourceFailedException
     * @throws InvalidTransformerException
     * @throws Throwable
     */
    public function setPrimaryMedia(SetPrimaryMediaRequest $request): array
    {
        $media = app(SetPrimaryMediaAction::class)->run($request->id);

        return $this->transform($media, MediaTransformer::class);
    }
}
