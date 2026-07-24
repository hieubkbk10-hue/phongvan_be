<?php

namespace App\Containers\AppSection\Media\Actions;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\UI\API\Requests\UploadMediaRequest;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadMediaAction extends ParentAction
{
    /**
     * @param UploadMediaRequest $request
     * @return Media
     * @throws CreateResourceFailedException
     * @throws ValidationFailedException
     */
    public function run(UploadMediaRequest $request): Media
    {
        $file = $request->file('file');
        $productId = (int) $request->product_id;
        $disk = (string) config('appSection-media.disk', 'public');
        $folder = 'products';

        // 1. Upload file bytes to Storage BEFORE DB transaction using storeAs to avoid finfo issues
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::random(40) . '.' . $extension;
        $path = $file->storeAs($folder, $filename, $disk);

        try {
            // 2. Short DB Transaction with lock
            return DB::transaction(function () use ($request, $file, $productId, $disk, $path) {
                // Lock Product row to check current photo count
                $product = Product::where('id', $productId)->lockForUpdate()->first();
                if (!$product) {
                    throw (new ValidationFailedException('Product not found.'))->withErrors(['product_id' => ['Product not found.']]);
                }

                $existingCount = Media::where('mediable_type', Product::class)
                    ->where('mediable_id', $productId)
                    ->count();

                if ($existingCount >= 9) {
                    throw (new ValidationFailedException('Upload Product không được vượt quá 9 ảnh.'))->withErrors(['file' => ['Upload Product không được vượt quá 9 ảnh.']]);
                }

                $isMain = ($existingCount === 0) ? true : (bool) ($request->is_main ?? false);

                if ($isMain) {
                    Media::where('mediable_type', Product::class)
                        ->where('mediable_id', $productId)
                        ->update(['is_main' => false]);
                }

                $sortOrder = $request->has('sort_order') ? (int) $request->sort_order : $existingCount;

                return Media::create([
                    'disk' => $disk,
                    'path' => $path,
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType() ?: 'image/jpeg',
                    'size' => $file->getSize(),
                    'sort_order' => $sortOrder,
                    'is_main' => $isMain,
                    'mediable_type' => Product::class,
                    'mediable_id' => $productId,
                ]);
            });
        } catch (ValidationFailedException $e) {
            // Remove uploaded file on validation error
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }

            throw $e;
        } catch (Exception $e) {
            // Remove uploaded file on general error
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }

            throw new CreateResourceFailedException($e->getMessage());
        }
    }
}
