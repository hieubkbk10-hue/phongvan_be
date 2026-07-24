<?php

namespace App\Containers\AppSection\Order\Actions;

use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Tasks\CalculateOrderTotalsTask;
use App\Containers\AppSection\Order\Tasks\CreateOrderItemsTask;
use App\Containers\AppSection\Order\Tasks\CreateOrderTask;
use App\Containers\AppSection\Order\Tasks\GenerateOrderCodeTask;
use App\Containers\AppSection\Order\Tasks\GetActiveProductsByIdsTask;
use App\Containers\AppSection\Order\Tasks\ResolveOrderCustomerTask;
use App\Containers\AppSection\Order\UI\API\Requests\CreateOrderRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

class CreateOrderAction extends ParentAction
{
    public function run(CreateOrderRequest $request): Order
    {
        $sanitized = $request->sanitizeInput([
            'customer_id',
            'customer_name_snapshot',
            'customer_phone_snapshot',
            'customer_address_snapshot',
            'payment_method',
            'delivery_date',
            'shipping_carrier',
            'bank_name',
            'bank_account_number',
            'credit_days',
            'shipping_fee',
            'advance_payment',
            'items',
        ]);

        return DB::transaction(function () use ($sanitized) {
            // 1. Resolve Customer
            $customerData = app(ResolveOrderCustomerTask::class)->run(
                $sanitized['customer_id'] ?? null,
                $sanitized['customer_name_snapshot'] ?? null,
                $sanitized['customer_phone_snapshot'] ?? null,
                $sanitized['customer_address_snapshot'] ?? null
            );

            // 2. Fetch Active Products
            $itemsInput = $sanitized['items'] ?? [];
            $productIds = array_column($itemsInput, 'product_id');
            $products = app(GetActiveProductsByIdsTask::class)->run($productIds);

            // 3. Calculate Totals & Items Snapshots
            $totals = app(CalculateOrderTotalsTask::class)->run(
                $itemsInput,
                $products,
                $sanitized['shipping_fee'] ?? 0,
                $sanitized['advance_payment'] ?? 0
            );

            // 4. Generate Unique Order Code
            $code = app(GenerateOrderCodeTask::class)->run();

            // 5. Create Order Record
            $orderData = [
                'code' => $code,
                'customer_id' => $customerData['customer_id'],
                'customer_name_snapshot' => $customerData['name_snapshot'],
                'customer_phone_snapshot' => $customerData['phone_snapshot'],
                'customer_address_snapshot' => $customerData['address_snapshot'],
                'payment_method' => $sanitized['payment_method'],
                'delivery_date' => $sanitized['delivery_date'] ?? null,
                'shipping_carrier' => $sanitized['shipping_carrier'] ?? null,
                'bank_name' => $sanitized['bank_name'] ?? null,
                'bank_account_number' => $sanitized['bank_account_number'] ?? null,
                'credit_days' => $sanitized['credit_days'] ?? null,
                'subtotal' => $totals['subtotal'],
                'shipping_fee' => $totals['shipping_fee'],
                'total_amount' => $totals['total_amount'],
                'advance_payment' => $totals['advance_payment'],
                'remaining_amount' => $totals['remaining_amount'],
                'status' => Order::STATUS_PENDING,
            ];

            /** @var Order $order */
            $order = app(CreateOrderTask::class)->run($orderData);

            // 6. Create Order Items
            app(CreateOrderItemsTask::class)->run($order->id, $totals['processed_items']);

            return $order->load('items');
        });
    }
}
