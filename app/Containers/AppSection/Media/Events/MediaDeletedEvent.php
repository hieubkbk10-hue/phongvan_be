<?php

namespace App\Containers\AppSection\Media\Events;

use App\Ship\Parents\Events\Event as ParentEvent;

class MediaDeletedEvent extends ParentEvent
{
    /**
     * @param string $path Đường dẫn file vật lý trên storage
     * @param string $disk Ổ đĩa lưu trữ (mặc định public)
     */
    public function __construct(
        public string $path,
        public string $disk = 'public'
    ) {
    }
}
