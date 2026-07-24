<?php

namespace App\Containers\AppSection\Order\Data\Repositories;

use App\Ship\Parents\Repositories\Repository as ParentRepository;

class OrderRepository extends ParentRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id' => '=',
        'code' => '=',
        'customer_name_snapshot' => 'like',
        'customer_phone_snapshot' => 'like',
        'status' => '=',
        'payment_method' => '=',
    ];
}
