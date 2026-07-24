<?php

namespace App\Containers\AppSection\Product\Models;

use App\Containers\AppSection\Media\Models\Media;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends ParentModel
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'price',
        'status',
    ];

    protected $hidden = [
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'integer',
    ];

    /**
     * A resource key to be used in the serialized responses.
     */
    protected string $resourceKey = 'Product';

    public static function getTableName(): string
    {
        return 'products';
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
