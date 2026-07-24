<?php

namespace App\Containers\AppSection\Order\Models;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends ParentModel
{
    public const STATUS_CANCELLED = 5;

    protected $table = 'orders';

    protected $fillable = [
        'code',
        'customer_id',
        'customer_name_snapshot',
        'customer_phone_snapshot',
        'customer_address_snapshot',
        'delivery_date',
        'shipping_carrier',
        'payment_method',
        'credit_days',
        'bank_name',
        'bank_account_number',
        'subtotal',
        'shipping_fee',
        'total_amount',
        'advance_payment',
        'remaining_amount',
        'status',
        'cancel_reason',
    ];

    protected $hidden = [
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'payment_method' => 'integer',
        'credit_days' => 'integer',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'status' => 'integer',
    ];

    /**
     * A resource key to be used in the serialized responses.
     */
    protected string $resourceKey = 'Order';

    public static function getTableName(): string
    {
        return 'orders';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
