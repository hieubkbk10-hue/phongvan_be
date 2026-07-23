<?php

namespace App\Containers\AppSection\Media\UI\API\Controllers;

use Apiato\Core\Exceptions\CoreInternalErrorException;
use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Media\Actions\GetAllMediaAction;
use App\Containers\AppSection\Media\UI\API\Requests\GetAllMediaRequest;
use App\Containers\AppSection\Media\UI\API\Transformers\MediaTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Prettus\Repository\Exceptions\RepositoryException;

class GetAllMediaController extends ApiController
{
    /**
     * @throws InvalidTransformerException
     * @throws CoreInternalErrorException
     * @throws RepositoryException
     */
    public function getAllMedia(GetAllMediaRequest $request): array
    {
        $media = app(GetAllMediaAction::class)->run($request);

        return $this->transform($media, MediaTransformer::class);
    }
}
