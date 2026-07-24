<?php

namespace App\Containers\AppSection\Order\UI\API\Transformers;

use App\Containers\AppSection\Order\Models\OrderItem;
use App\Containers\AppSection\Product\UI\API\Transformers\ProductTransformer;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;

class OrderItemTransformer extends ParentTransformer
{
    protected array $availableIncludes = [
        'product',
    ];

    public function transform(OrderItem $orderItem): array
    {
        $response = [
            'object' => $orderItem->getResourceKey(),
            'id' => $orderItem->getHashedKey(),
            'product_id' => $orderItem->product_id ? $orderItem->encode($orderItem->product_id) : null,
            'product_name_snapshot' => $orderItem->product_name_snapshot,
            'product_price_snapshot' => $orderItem->product_price_snapshot,
            'unit_price' => $orderItem->unit_price,
            'price_override_reason' => $orderItem->price_override_reason,
            'quantity' => $orderItem->quantity,
            'total_item_price' => $orderItem->total_item_price,
        ];

        return $this->ifAdmin([
            'real_id' => $orderItem->id,
            'created_at' => $orderItem->created_at,
            'updated_at' => $orderItem->updated_at,
            'readable_created_at' => $orderItem->created_at?->diffForHumans(),
            'readable_updated_at' => $orderItem->updated_at?->diffForHumans(),
        ], $response);
    }

    public function includeProduct(OrderItem $orderItem): Item|NullResource
    {
        if ($orderItem->product === null) {
            return $this->null();
        }

        return $this->item($orderItem->product, new ProductTransformer());
    }
}
