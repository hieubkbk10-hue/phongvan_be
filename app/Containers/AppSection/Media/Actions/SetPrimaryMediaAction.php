<?php

namespace App\Containers\AppSection\Media\Actions;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\FindMediaByIdTask;
use App\Containers\AppSection\Media\Tasks\UpdateMediaTask;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Throwable;

class SetPrimaryMediaAction extends ParentAction
{
    /**
     * Đặt 1 Media làm ảnh chính (primary/main) cho đối tượng sở hữu.
     *
     * @param int|Media $mediaOrId
     * @return Media
     * @throws NotFoundException
     * @throws UpdateResourceFailedException
     * @throws Throwable
     */
    public function run(int|Media $mediaOrId): Media
    {
        $media = $mediaOrId instanceof Media
            ? $mediaOrId
            : app(FindMediaByIdTask::class)->run($mediaOrId);

        return DB::transaction(function () use ($media) {
            // Reset tất cả các ảnh khác của cùng đối tượng sở hữu về is_main = false
            if ($media->mediable_type && $media->mediable_id) {
                Media::query()
                    ->where('mediable_type', $media->mediable_type)
                    ->where('mediable_id', $media->mediable_id)
                    ->where('id', '!=', $media->id)
                    ->update(['is_main' => false]);
            }

            // Đặt ảnh chỉ định thành is_main = true
            return app(UpdateMediaTask::class)->run(['is_main' => true], $media->id);
        });
    }
}
