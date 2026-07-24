<?php

namespace App\Containers\AppSection\Product\UI\API\Requests;

use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends ParentRequest
{
    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    protected array $decode = [
        'id',
    ];

    protected array $urlParameters = [
        'id',
    ];

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', Rule::exists(Product::getTableName(), 'id')],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'required', 'integer', Rule::in([Product::STATUS_INACTIVE, Product::STATUS_ACTIVE])],
        ];
    }

    public function authorize(): bool
    {
        return $this->check([
            'hasAccess',
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $allowed = ['id', 'name', 'price', 'status'];
            $inputKeys = array_keys($this->all());
            $extra = array_diff($inputKeys, $allowed);
            if (!empty($extra)) {
                $validator->errors()->add('fields', 'Unallowed fields present.');
            }
        });
    }
}
