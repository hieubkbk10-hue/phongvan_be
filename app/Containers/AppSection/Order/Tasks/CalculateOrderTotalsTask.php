<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Collection;

class CalculateOrderTotalsTask extends ParentTask
{
    private const MAX_CENTS = 999999999999999; // 9,999,999,999,999.99 in cents

    /**
     * @param array<array{product_id: int, quantity: int, unit_price?: string|float|int|null, price_override_reason?: string|null}> $itemsInput
     * @param Collection<int, Product> $products
     * @param string|float|int|null $shippingFee
     * @param string|float|int|null $advancePayment
     * @return array{subtotal: string, shipping_fee: string, total_amount: string, advance_payment: string, remaining_amount: string, processed_items: array}
     * @throws ValidationFailedException
     */
    public function run(
        array $itemsInput,
        Collection $products,
        string|float|int|null $shippingFee = 0,
        string|float|int|null $advancePayment = 0
    ): array {
        $subtotalCents = 0;
        $processedItems = [];

        foreach ($itemsInput as $index => $itemInput) {
            $productId = (int) $itemInput['product_id'];
            /** @var Product $product */
            $product = $products->get($productId);

            $catalogPriceCents = $this->parseMoneyToCents($product->price);
            $catalogPriceStr = $this->formatCentsToString($catalogPriceCents);

            if (isset($itemInput['unit_price']) && $itemInput['unit_price'] !== null && $itemInput['unit_price'] !== '') {
                $unitPriceCents = $this->parseMoneyToCents($itemInput['unit_price']);
                $unitPriceStr = $this->formatCentsToString($unitPriceCents);
            } else {
                $unitPriceCents = $catalogPriceCents;
                $unitPriceStr = $catalogPriceStr;
            }

            // Reason check on price override
            $reason = null;
            if ($unitPriceCents !== $catalogPriceCents) {
                if (empty($itemInput['price_override_reason']) || trim((string) $itemInput['price_override_reason']) === '') {
                    throw (new ValidationFailedException('Price override reason is required when unit price differs from product price.'))
                        ->withErrors(["items.{$index}.price_override_reason" => ['Price override reason is required when unit price differs from product price.']]);
                }
                $reason = trim((string) $itemInput['price_override_reason']);
            }

            $qty = (int) $itemInput['quantity'];
            $lineTotalCents = $unitPriceCents * $qty;

            if ($lineTotalCents > self::MAX_CENTS || $lineTotalCents < 0) {
                throw (new ValidationFailedException('Item total price exceeds maximum allowed limit.'))
                    ->withErrors(["items.{$index}.total_item_price" => ['Item total price exceeds maximum allowed limit.']]);
            }

            $subtotalCents += $lineTotalCents;
            if ($subtotalCents > self::MAX_CENTS) {
                throw (new ValidationFailedException('Subtotal amount exceeds maximum allowed limit.'))
                    ->withErrors(['subtotal' => ['Subtotal amount exceeds maximum allowed limit.']]);
            }

            $processedItems[] = [
                'product_id' => $product->id,
                'product_name_snapshot' => $product->name,
                'product_price_snapshot' => $catalogPriceStr,
                'unit_price' => $unitPriceStr,
                'price_override_reason' => $reason,
                'quantity' => $qty,
                'total_item_price' => $this->formatCentsToString($lineTotalCents),
            ];
        }

        $shippingFeeCents = $this->parseMoneyToCents($shippingFee ?? 0);
        $totalAmountCents = $subtotalCents + $shippingFeeCents;

        if ($totalAmountCents > self::MAX_CENTS) {
            throw (new ValidationFailedException('Total amount exceeds maximum allowed limit.'))
                ->withErrors(['total_amount' => ['Total amount exceeds maximum allowed limit.']]);
        }

        $advancePaymentCents = $this->parseMoneyToCents($advancePayment ?? 0);

        if ($advancePaymentCents > $totalAmountCents) {
            throw (new ValidationFailedException('Advance payment cannot exceed total amount.'))
                ->withErrors(['advance_payment' => ['Advance payment cannot exceed total amount.']]);
        }

        $remainingAmountCents = $totalAmountCents - $advancePaymentCents;

        return [
            'subtotal' => $this->formatCentsToString($subtotalCents),
            'shipping_fee' => $this->formatCentsToString($shippingFeeCents),
            'total_amount' => $this->formatCentsToString($totalAmountCents),
            'advance_payment' => $this->formatCentsToString($advancePaymentCents),
            'remaining_amount' => $this->formatCentsToString($remainingAmountCents),
            'processed_items' => $processedItems,
        ];
    }

    private function parseMoneyToCents(string|float|int|null $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $str = (string) $value;
        if (!is_numeric($str)) {
            throw (new ValidationFailedException('Invalid money format.'))->withErrors(['money' => ['Invalid money format.']]);
        }

        $cents = (int) round(((float) $str) * 100);

        if ($cents < 0 || $cents > self::MAX_CENTS) {
            throw (new ValidationFailedException('Amount exceeds maximum allowed limit.'))->withErrors(['money' => ['Amount exceeds maximum allowed limit.']]);
        }

        return $cents;
    }

    private function formatCentsToString(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}
