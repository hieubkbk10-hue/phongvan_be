<?php

namespace App\Containers\AppSection\Media\Listeners;

use App\Containers\AppSection\Media\Events\MediaDeletedEvent;
use App\Containers\AppSection\Media\Tasks\DeleteMediaFileTask;
use App\Ship\Parents\Listeners\Listener as ParentListener;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;

class DeleteMediaFileListener extends ParentListener implements ShouldQueue
{
    /**
     * Chỉ kích hoạt listener sau khi tất cả các Database Transactions đã commit thành công.
     *
     * @var bool
     */
    public bool $afterCommit = true;

    /**
     * Xử lý sự kiện xóa file vật lý tương ứng.
     *
     * @param MediaDeletedEvent $event
     * @return void
     * @throws Throwable
     */
    public function handle(MediaDeletedEvent $event): void
    {
        app(DeleteMediaFileTask::class)->run($event->path, $event->disk);
    }
}
