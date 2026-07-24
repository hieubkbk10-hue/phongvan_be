<?php

namespace App\Containers\AppSection\Order\Models;

use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends ParentModel
{
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name_snapshot',
        'product_price_snapshot',
        'unit_price',
        'price_override_reason',
        'quantity',
        'total_item_price',
    ];

    protected $hidden = [
    ];

    protected $casts = [
        'order_id' => 'integer',
        'product_id' => 'integer',
        'product_price_snapshot' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'total_item_price' => 'decimal:2',
    ];

    /**
     * A resource key to be used in the serialized responses.
     */
    protected string $resourceKey = 'OrderItem';

    public static function getTableName(): string
    {
        return 'order_items';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
