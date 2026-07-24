<?php

namespace App\Containers\AppSection\Product\Tasks;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Product\Data\Repositories\ProductRepository;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteProductTask extends ParentTask
{
    public function __construct(
        protected ProductRepository $repository
    ) {
    }

    /**
     * @throws DeleteResourceFailedException
     * @throws NotFoundException
     */
    public function run($id): int
    {
        try {
            /** @var Product $product */
            $product = $this->repository->find($id);

            return DB::transaction(function () use ($product) {
                // Collect media files to delete physical files after DB transaction
                $mediaList = $product->media()->get();
                $filesToDelete = [];
                /** @var Media $media */
                foreach ($mediaList as $media) {
                    $filesToDelete[] = [
                        'disk' => $media->disk,
                        'path' => $media->path,
                    ];
                }

                // Delete media database records
                $product->media()->delete();

                // Delete product record (hard delete)
                $result = $product->delete();

                // Delete physical storage files
                foreach ($filesToDelete as $file) {
                    if (Storage::disk($file['disk'])->exists($file['path'])) {
                        Storage::disk($file['disk'])->delete($file['path']);
                    }
                }

                return (int) $result;
            });
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        } catch (Exception $e) {
            throw new DeleteResourceFailedException($e->getMessage());
        }
    }
}
