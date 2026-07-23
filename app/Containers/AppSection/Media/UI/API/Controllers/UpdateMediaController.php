<?php

namespace App\Containers\AppSection\Media\UI\API\Controllers;

use Apiato\Core\Exceptions\IncorrectIdException;
use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Media\Actions\UpdateMediaAction;
use App\Containers\AppSection\Media\UI\API\Requests\UpdateMediaRequest;
use App\Containers\AppSection\Media\UI\API\Transformers\MediaTransformer;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Controllers\ApiController;

class UpdateMediaController extends ApiController
{
    /**
     * @param UpdateMediaRequest $request
     * @return array
     * @throws InvalidTransformerException
     * @throws UpdateResourceFailedException
     * @throws IncorrectIdException
     * @throws NotFoundException
     */
    public function updateMedia(UpdateMediaRequest $request): array
    {
        $media = app(UpdateMediaAction::class)->run($request);

        return $this->transform($media, MediaTransformer::class);
    }
}
