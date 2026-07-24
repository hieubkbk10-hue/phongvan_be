<?php

namespace App\Containers\AppSection\Media\Actions;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\UI\API\Requests\DeleteMediaRequest;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteMediaAction extends ParentAction
{
    /**
     * @param DeleteMediaRequest $request
     * @return void
     * @throws ValidationFailedException
     */
    public function run(DeleteMediaRequest $request): void
    {
        $productId = (int) $request->product_id;
        $mediaId = (int) $request->id;

        /** @var Media|null $media */
        $media = Media::where('id', $mediaId)
            ->where('mediable_type', Product::class)
            ->where('mediable_id', $productId)
            ->first();

        if (!$media) {
            throw (new ValidationFailedException('Media does not belong to the specified product.'))->withErrors(['id' => ['Media does not belong to the specified product.']]);
        }

        $filePath = $media->path;
        $fileDisk = $media->disk;
        $wasPrimary = (bool) $media->is_main;

        DB::transaction(function () use ($productId, $media, $wasPrimary) {
            // Lock Product row
            Product::where('id', $productId)->lockForUpdate()->first();

            // Delete DB record
            $media->delete();

            // If deleted media was primary photo, promote smallest (sort_order, id) to be new primary
            if ($wasPrimary) {
                /** @var Media|null $nextPrimary */
                $nextPrimary = Media::where('mediable_type', Product::class)
                    ->where('mediable_id', $productId)
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('id', 'asc')
                    ->first();

                if ($nextPrimary) {
                    $nextPrimary->update(['is_main' => true]);
                }
            }
        });

        // Delete physical file after DB commit succeeds
        if (Storage::disk($fileDisk)->exists($filePath)) {
            Storage::disk($fileDisk)->delete($filePath);
        }
    }
}
