<?php

namespace App\Containers\AppSection\Media\UI\API\Transformers;

use App\Containers\AppSection\Media\Models\Media;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

class MediaTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    public function transform(Media $media): array
    {
        /** @psalm-suppress UndefinedFunction */
        $url = storage_url($media->path, $media->disk);

        $response = [
            'object' => $media->getResourceKey(),
            'id' => $media->getHashedKey(),
            'url' => $url,
            'filename' => $media->filename,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'sort_order' => $media->sort_order,
            'is_primary' => $media->is_main,
        ];

        return $this->ifAdmin([
            'real_id' => $media->id,
            'disk' => $media->disk,
            'path' => $media->path,
            'created_at' => $media->created_at,
            'updated_at' => $media->updated_at,
            'readable_created_at' => $media->created_at?->diffForHumans(),
            'readable_updated_at' => $media->updated_at?->diffForHumans(),
        ], $response);
    }
}
