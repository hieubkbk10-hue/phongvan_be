<?php

namespace App\Containers\AppSection\Media\Tasks;

use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Exception;
use Illuminate\Support\Facades\Storage;

class DeleteMediaFileTask extends ParentTask
{
    /**
     * Xóa file vật lý khỏi storage disk.
     *
     * @throws DeleteResourceFailedException
     */
    public function run(string $path, string $disk = 'public'): bool
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->delete($path);
            }

            return true;
        } catch (Exception) {
            throw new DeleteResourceFailedException();
        }
    }
}
