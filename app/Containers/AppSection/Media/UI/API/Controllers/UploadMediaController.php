<?php

namespace App\Containers\AppSection\Media\UI\API\Controllers;

use Apiato\Core\Exceptions\InvalidTransformerException;
use App\Containers\AppSection\Media\Actions\UploadMediaAction;
use App\Containers\AppSection\Media\UI\API\Requests\UploadMediaRequest;
use App\Containers\AppSection\Media\UI\API\Transformers\MediaTransformer;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Throwable;

class UploadMediaController extends ApiController
{
    /**
     * @param UploadMediaRequest $request
     * @return JsonResponse
     * @throws CreateResourceFailedException
     * @throws InvalidTransformerException
     * @throws Throwable
     */
    public function uploadMedia(UploadMediaRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $data = $request->sanitizeInput([
            'mediable_type',
            'mediable_id',
            'is_main',
            'sort_order',
            'disk',
        ]);

        $media = app(UploadMediaAction::class)->run($file, $data);

        return $this->created($this->transform($media, MediaTransformer::class));
    }
}
