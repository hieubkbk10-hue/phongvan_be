<?php

namespace App\Containers\AppSection\Media\Data\Repositories;

use App\Containers\AppSection\Media\Models\Media;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

class MediaRepository extends ParentRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'mediable_type' => '=',
        'is_main' => '=',
        'sort_order' => '=',
    ];

    public function model(): string
    {
        return Media::class;
    }
}
