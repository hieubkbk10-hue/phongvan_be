<?php

namespace App\Containers\AppSection\Product\Models;

use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends ParentModel
{
    use SoftDeletes;

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
}

