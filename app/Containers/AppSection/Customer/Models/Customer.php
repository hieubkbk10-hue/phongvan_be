<?php

namespace App\Containers\AppSection\Customer\Models;

use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends ParentModel
{
    use SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'phone',
        'address',
        'email',
    ];

    protected $hidden = [
    ];

    protected $casts = [
    ];

    /**
     * A resource key to be used in the serialized responses.
     */
    protected string $resourceKey = 'Customer';

    public static function getTableName(): string
    {
        return 'customers';
    }
}
