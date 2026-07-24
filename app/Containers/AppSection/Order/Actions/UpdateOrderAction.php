<?php

namespace App\Containers\AppSection\Order\Actions;

use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Tasks\CalculateOrderTotalsTask;
use App\Containers\AppSection\Order\Tasks\FindOrderForUpdateTask;
use App\Containers\AppSection\Order\Tasks\GetActiveProductsByIdsTask;
use App\Containers\AppSection\Order\Tasks\ReplaceOrderItemsTask;
use App\Containers\AppSection\Order\Tasks\ResolveOrderCustomerTask;
use App\Containers\AppSection\Order\Tasks\UpdateOrderTask;
use App\Containers\AppSection\Order\UI\API\Requests\UpdateOrderRequest;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

class UpdateOrderAction extends ParentAction
{
    public function run(UpdateOrderRequest $request): Order
    {
        $sanitized = $request->sanitizeInput([
            'id',
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

        $orderId = (int) $request->id;

        return DB::transaction(function () use ($orderId, $sanitized, $request) {
            /** @var Order $order */
            $order = app(FindOrderForUpdateTask::class)->run($orderId);

            if ((int) $order->status !== Order::STATUS_PENDING) {
                throw (new ValidationFailedException('Completed or Cancelled orders cannot be updated.'))
                    ->withErrors(['status' => ['Completed or Cancelled orders cannot be updated.']]);
            }

            $updateData = [];

            // Update Customer info if provided
            if ($request->has('customer_id') || $request->has('customer_name_snapshot') || $request->has('customer_phone_snapshot') || $request->has('customer_address_snapshot')) {
                $customerId = $request->has('customer_id') ? ($sanitized['customer_id'] ?? null) : $order->customer_id;
                $name = $sanitized['customer_name_snapshot'] ?? $order->customer_name_snapshot;
                $phone = $sanitized['customer_phone_snapshot'] ?? $order->customer_phone_snapshot;
                $address = $sanitized['customer_address_snapshot'] ?? $order->customer_address_snapshot;

                $customerData = app(ResolveOrderCustomerTask::class)->run($customerId, $name, $phone, $address);
                $updateData['customer_id'] = $customerData['customer_id'];
                $updateData['customer_name_snapshot'] = $customerData['name_snapshot'];
                $updateData['customer_phone_snapshot'] = $customerData['phone_snapshot'];
                $updateData['customer_address_snapshot'] = $customerData['address_snapshot'];
            }

            // Simple fields
            foreach (['payment_method', 'delivery_date', 'shipping_carrier', 'bank_name', 'bank_account_number', 'credit_days'] as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $sanitized[$field] ?? null;
                }
            }

            // Items and Totals update
            if ($request->has('items')) {
                $itemsInput = $sanitized['items'] ?? [];
                $productIds = array_column($itemsInput, 'product_id');
                $products = app(GetActiveProductsByIdsTask::class)->run($productIds);

                $shippingFee = $request->has('shipping_fee') ? ($sanitized['shipping_fee'] ?? 0) : $order->shipping_fee;
                $advancePayment = $request->has('advance_payment') ? ($sanitized['advance_payment'] ?? 0) : $order->advance_payment;

                $totals = app(CalculateOrderTotalsTask::class)->run($itemsInput, $products, $shippingFee, $advancePayment);

                $updateData['subtotal'] = $totals['subtotal'];
                $updateData['shipping_fee'] = $totals['shipping_fee'];
                $updateData['total_amount'] = $totals['total_amount'];
                $updateData['advance_payment'] = $totals['advance_payment'];
                $updateData['remaining_amount'] = $totals['remaining_amount'];

                app(ReplaceOrderItemsTask::class)->run($order->id, $totals['processed_items']);
            } elseif ($request->has('shipping_fee') || $request->has('advance_payment')) {
                $shippingFee = $request->has('shipping_fee') ? ($sanitized['shipping_fee'] ?? 0) : $order->shipping_fee;
                $advancePayment = $request->has('advance_payment') ? ($sanitized['advance_payment'] ?? 0) : $order->advance_payment;

                // Re-calculate with existing items
                $existingItems = $order->items()->get();
                $existingProductIds = $existingItems->pluck('product_id')->filter()->toArray();
                $products = empty($existingProductIds) ? collect() : app(GetActiveProductsByIdsTask::class)->run($existingProductIds);

                $itemsInput = $existingItems->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'price_override_reason' => $item->price_override_reason,
                    ];
                })->toArray();

                $totals = app(CalculateOrderTotalsTask::class)->run($itemsInput, $products, $shippingFee, $advancePayment);

                $updateData['shipping_fee'] = $totals['shipping_fee'];
                $updateData['total_amount'] = $totals['total_amount'];
                $updateData['advance_payment'] = $totals['advance_payment'];
                $updateData['remaining_amount'] = $totals['remaining_amount'];
            }

            if (!empty($updateData)) {
                $order = app(UpdateOrderTask::class)->run($updateData, $order->id);
            }

            return $order->load('items');
        });
    }
}
