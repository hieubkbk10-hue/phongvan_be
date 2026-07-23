<?php

namespace App\Containers\AppSection\Media\UI\API\Controllers;

use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Media\Actions\FindMediaByIdAction;
use App\Containers\AppSection\Media\UI\API\Requests\FindMediaByIdRequest;
use App\Containers\AppSection\Media\UI\API\Transformers\MediaTransformer;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Controllers\ApiController;

class FindMediaByIdController extends ApiController
{
    /**
     * @throws InvalidTransformerException|NotFoundException
     */
    public function findMediaById(FindMediaByIdRequest $request): array
    {
        $media = app(FindMediaByIdAction::class)->run($request);

        return $this->transform($media, MediaTransformer::class);
    }
}
