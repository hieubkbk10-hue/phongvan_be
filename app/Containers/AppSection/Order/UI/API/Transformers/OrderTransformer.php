<?php

namespace App\Containers\AppSection\Order\UI\API\Transformers;

use App\Containers\AppSection\Customer\UI\API\Transformers\CustomerTransformer;
use App\Containers\AppSection\Order\Models\Order;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;

class OrderTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'customer',
        'items',
    ];

    public function transform(Order $order): array
    {
        $response = [
            'object' => $order->getResourceKey(),
            'id' => $order->getHashedKey(),
            'code' => $order->code,
            'customer_name_snapshot' => $order->customer_name_snapshot,
            'customer_phone_snapshot' => $order->customer_phone_snapshot,
            'customer_address_snapshot' => $order->customer_address_snapshot,
            'delivery_date' => $order->delivery_date?->toDateString(),
            'shipping_carrier' => $order->shipping_carrier,
            'payment_method' => $order->payment_method,
            'credit_days' => $order->credit_days,
            'bank_name' => $order->bank_name,
            'bank_account_number' => $order->bank_account_number,
            'subtotal' => $order->subtotal,
            'shipping_fee' => $order->shipping_fee,
            'total_amount' => $order->total_amount,
            'advance_payment' => $order->advance_payment,
            'remaining_amount' => $order->remaining_amount,
            'status' => $order->status,
            'cancel_reason' => $order->cancel_reason,
        ];

        return $this->ifAdmin([
            'real_id' => $order->id,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'readable_created_at' => $order->created_at?->diffForHumans(),
            'readable_updated_at' => $order->updated_at?->diffForHumans(),
        ], $response);
    }

    public function includeCustomer(Order $order): Item|NullResource
    {
        // if ($order->customer === null) {
        //     return $this->null();
        // }

        return $this->nullableItem($order->customer, new CustomerTransformer());
    }

    public function includeItems(Order $order): Collection
    {
        return $this->collection($order->items, new OrderItemTransformer());
    }
}
