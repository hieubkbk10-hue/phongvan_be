<?php

namespace App\Containers\AppSection\Media\Actions;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\UI\API\Requests\ReorderMediaRequest;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

class ReorderProductMediaAction extends ParentAction
{
    /**
     * @param ReorderMediaRequest $request
     * @return void
     * @throws ValidationFailedException
     */
    public function run(ReorderMediaRequest $request): void
    {
        $productId = (int) $request->product_id;
        $mediaList = (array) $request->media;

        $payloadIds = array_map('intval', array_column($mediaList, 'id'));

        // Check if all media IDs belong to product and match total product media count
        $productTotalMediaCount = Media::where('mediable_type', Product::class)
            ->where('mediable_id', $productId)
            ->count();

        $matchingMediaCount = Media::whereIn('id', $payloadIds)
            ->where('mediable_type', Product::class)
            ->where('mediable_id', $productId)
            ->count();

        if (count($payloadIds) !== $matchingMediaCount || count($payloadIds) !== $productTotalMediaCount) {
            throw (new ValidationFailedException('Media list must contain all and only media belonging to the product.'))->withErrors(['media' => ['Media list must contain all and only media belonging to the product.']]);
        }

        DB::transaction(function () use ($productId, $mediaList, $payloadIds) {
            // Lock Product row first
            Product::where('id', $productId)->lockForUpdate()->first();

            // Lock Media rows ordered by ID asc to prevent deadlocks
            $idsAsc = $payloadIds;
            sort($idsAsc);
            Media::whereIn('id', $idsAsc)->orderBy('id', 'asc')->lockForUpdate()->get();

            foreach ($mediaList as $item) {
                Media::where('id', (int) $item['id'])
                    ->update(['sort_order' => (int) $item['sort_order']]);
            }
        });
    }
}
