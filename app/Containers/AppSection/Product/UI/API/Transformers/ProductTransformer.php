<?php

namespace App\Containers\AppSection\Product\UI\API\Transformers;

use App\Containers\AppSection\Media\UI\API\Transformers\MediaTransformer;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;
use League\Fractal\Resource\Collection;

class ProductTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [

    ];

    protected array $availableIncludes = [
        'media',
    ];

    public function transform(Product $product): array
    {
        $response = [
            'object' => $product->getResourceKey(),
            'id' => $product->getHashedKey(),
            'name' => $product->name,
            'price' => number_format((float) $product->price, 2, '.', ''),
            'status' => (int) $product->status,
        ];

        return $this->ifAdmin([
            'real_id' => $product->id,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'readable_created_at' => $product->created_at?->diffForHumans(),
            'readable_updated_at' => $product->updated_at?->diffForHumans(),
        ], $response);
    }

    public function includeMedia(Product $product): Collection
    {
        return $this->collection($product->media, new MediaTransformer());
    }
}
