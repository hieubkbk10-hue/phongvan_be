<?php

namespace App\Containers\AppSection\Media\Actions;

use App\Containers\AppSection\Media\Events\MediaDeletedEvent;
use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\DeleteMediaTask;
use App\Containers\AppSection\Media\Tasks\FindMediaByIdTask;
use App\Containers\AppSection\Media\UI\API\Requests\DeleteMediaRequest;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteMediaAction extends ParentAction
{
    /**
     * Xóa Media record và phát sự kiện xóa file vật lý tương ứng sau khi DB commit.
     *
     * @param DeleteMediaRequest|int|Media $target
     * @return int
     * @throws DeleteResourceFailedException
     * @throws NotFoundException
     * @throws Throwable
     */
    public function run(DeleteMediaRequest|int|Media $target): int
    {
        if ($target instanceof DeleteMediaRequest) {
            $media = app(FindMediaByIdTask::class)->run($target->id);
        } elseif ($target instanceof Media) {
            $media = $target;
        } else {
            $media = app(FindMediaByIdTask::class)->run($target);
        }

        $path = $media->path;
        $disk = $media->disk;

        return DB::transaction(function () use ($media, $path, $disk) {
            $result = app(DeleteMediaTask::class)->run($media->id);

            // Bắn MediaDeletedEvent -> Listener chạy xóa file vật lý sau khi DB commit thành công
            MediaDeletedEvent::dispatch($path, $disk);

            return $result;
        });
    }
}
