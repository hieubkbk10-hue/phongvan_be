<?php

namespace App\Containers\AppSection\Order\Data\Repositories;

use App\Containers\AppSection\Order\Models\OrderItem;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

class OrderItemRepository extends ParentRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id' => '=',
        'order_id' => '=',
        'product_id' => '=',
    ];

    public function model(): string
    {
        return OrderItem::class;
    }
}
