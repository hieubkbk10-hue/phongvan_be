<?php

namespace App\Containers\AppSection\Order\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends ParentRequest
{
    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    protected array $decode = [
        'customer_id',
        'items.*.product_id',
    ];

    protected array $urlParameters = [
    ];

    public function rules(): array
    {
        return [
            'code' => ['prohibited'],
            'status' => ['prohibited'],
            'subtotal' => ['prohibited'],
            'total_amount' => ['prohibited'],
            'remaining_amount' => ['prohibited'],
            'cancel_reason' => ['prohibited'],

            'customer_id' => ['nullable', Rule::exists('customers', 'id')],
            'customer_name_snapshot' => ['required_without:customer_id', 'nullable', 'string', 'max:150'],
            'customer_phone_snapshot' => ['required_without:customer_id', 'nullable', 'string', 'regex:/^\+?[0-9\-\s\(\)]{8,20}$/'],
            'customer_address_snapshot' => ['required_without:customer_id', 'nullable', 'string', 'max:500'],

            'payment_method' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'delivery_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'shipping_carrier' => ['nullable', 'string', 'max:100'],

            'bank_name' => ['required_if:payment_method,3', 'nullable', 'string', 'max:100'],
            'bank_account_number' => ['required_if:payment_method,3', 'nullable', 'string', 'max:50'],
            'credit_days' => ['required_if:payment_method,4', 'nullable', 'integer', 'min:1', 'max:365'],

            'shipping_fee' => ['nullable', 'numeric', 'min:0'],
            'advance_payment' => ['nullable', 'numeric', 'min:0'],

            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*' => ['required', 'array:product_id,quantity,unit_price,price_override_reason'],
            'items.*.product_id' => ['required', 'distinct', Rule::exists('products', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.price_override_reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $allowedTop = [
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
            ];

            $inputKeys = array_keys($this->all());
            $extra = array_diff($inputKeys, $allowedTop);
            if (!empty($extra)) {
                $validator->errors()->add('fields', 'Unallowed fields present.');
            }

            if ($this->has('items') && is_array($this->items)) {
                $allowedItemKeys = ['product_id', 'quantity', 'unit_price', 'price_override_reason'];
                foreach ($this->items as $index => $item) {
                    if (is_array($item)) {
                        $extraItemKeys = array_diff(array_keys($item), $allowedItemKeys);
                        if (!empty($extraItemKeys)) {
                            $validator->errors()->add("items.{$index}", 'Unallowed item parameters present.');
                        }
                    }
                }
            }
        });
    }

    public function authorize(): bool
    {
        return $this->check([
            'hasAccess',
        ]);
    }
}
