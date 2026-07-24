<?php

namespace App\Containers\AppSection\Order\UI\API\Transformers;

use App\Containers\AppSection\Order\Models\OrderItem;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

class OrderItemTransformer extends ParentTransformer
{
    public function transform(OrderItem $orderItem): array
    {
        $response = [
            'object' => $orderItem->getResourceKey(),
            'id' => $orderItem->getHashedKey(),
            'product_name_snapshot' => $orderItem->product_name_snapshot,
            'unit_price' => $orderItem->unit_price,
            'quantity' => $orderItem->quantity,
            'total_item_price' => $orderItem->total_item_price,
        ];

        return $this->ifAdmin([
            'real_id' => $orderItem->id,
            'created_at' => $orderItem->created_at,
            'updated_at' => $orderItem->updated_at,
            'readable_created_at' => $orderItem->created_at->diffForHumans(),
            'readable_updated_at' => $orderItem->updated_at->diffForHumans(),
        ], $response);
    }
}
