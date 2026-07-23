<?php

namespace App\Containers\AppSection\Media\Actions;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\CreateMediaTask;
use App\Containers\AppSection\Media\Tasks\DeleteMediaFileTask;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

class UploadMediaAction extends ParentAction
{
    /**
     * Upload file vật lý và tạo Media record trong Database.
     *
     * @param UploadedFile $file
     * @param array $data Thông tin bổ sung (disk, sort_order, is_main, mediable_type, mediable_id)
     * @return Media
     * @throws CreateResourceFailedException
     * @throws Throwable
     */
    public function run(UploadedFile $file, array $data = []): Media
    {
        $disk = $data['disk'] ?? 'public';
        $folder = 'media';

        // 1. Upload file vật lý lên storage disk
        $path = $file->store($folder, $disk);

        try {
            // 2. Ghi database trong transaction
            return DB::transaction(function () use ($file, $data, $path, $disk) {
                $isMain = (bool) ($data['is_main'] ?? false);
                $mediableType = $data['mediable_type'] ?? null;
                $mediableId = $data['mediable_id'] ?? null;

                // Nếu đặt làm ảnh chính, reset các ảnh chính cũ của cùng đối tượng
                if ($isMain && $mediableType && $mediableId) {
                    Media::query()
                        ->where('mediable_type', $mediableType)
                        ->where('mediable_id', $mediableId)
                        ->update(['is_main' => false]);
                }

                $mediaData = [
                    'disk' => $disk,
                    'path' => $path,
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_main' => $isMain,
                    'mediable_type' => $mediableType,
                    'mediable_id' => $mediableId,
                ];

                return app(CreateMediaTask::class)->run($mediaData);
            });
        } catch (Throwable $e) {
            // 3. Nếu DB lỗi sau upload, xóa file vừa upload để tránh rác storage
            app(DeleteMediaFileTask::class)->run($path, $disk);

            throw new CreateResourceFailedException();
        }
    }
}
