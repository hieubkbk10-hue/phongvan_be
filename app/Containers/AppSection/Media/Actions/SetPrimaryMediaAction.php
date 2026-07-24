<?php

namespace App\Containers\AppSection\Media\Actions;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\UI\API\Requests\SetPrimaryMediaRequest;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

class SetPrimaryMediaAction extends ParentAction
{
    /**
     * @param SetPrimaryMediaRequest $request
     * @return Media
     * @throws NotFoundException
     * @throws ValidationFailedException
     */
    public function run(SetPrimaryMediaRequest $request): Media
    {
        $productId = (int) $request->product_id;
        $mediaId = (int) $request->id;

        // Check if media belongs to specified product
        /** @var Media|null $media */
        $media = Media::where('id', $mediaId)
            ->where('mediable_type', Product::class)
            ->where('mediable_id', $productId)
            ->first();

        if (!$media) {
            throw (new ValidationFailedException('Media does not belong to the specified product.'))->withErrors(['id' => ['Media does not belong to the specified product.']]);
        }

        return DB::transaction(function () use ($productId, $media) {
            // Lock Product row to avoid race condition
            Product::where('id', $productId)->lockForUpdate()->first();

            // Unset is_main on all media of this product
            Media::where('mediable_type', Product::class)
                ->where('mediable_id', $productId)
                ->update(['is_main' => false]);

            // Set is_main on target media
            $media->update(['is_main' => true]);

            return $media->fresh();
        });
    }
}
