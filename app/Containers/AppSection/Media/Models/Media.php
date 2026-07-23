<?php

namespace App\Containers\AppSection\Media\Models;

use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends ParentModel
{
    protected $table = 'media';

    protected $fillable = [
        'name',
        'file_path',
        'disk',
        'mime_type',
        'size',
        'mediable_type',
        'mediable_id',
    ];

    protected $hidden = [
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * A resource key to be used in the serialized responses.
     */
    protected string $resourceKey = 'Media';

    public static function getTableName()
    {
        return 'media';
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
