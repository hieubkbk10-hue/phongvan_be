<?php

namespace App\Containers\AppSection\Media\Providers;

use App\Containers\AppSection\Media\Events\MediaDeletedEvent;
use App\Containers\AppSection\Media\Listeners\DeleteMediaFileListener;
use App\Ship\Parents\Providers\EventServiceProvider as ParentEventServiceProvider;

class EventServiceProvider extends ParentEventServiceProvider
{
    /**
     * Khai báo mapping giữa Event và Listener cho Media Container.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        MediaDeletedEvent::class => [
            DeleteMediaFileListener::class,
        ],
    ];
}
