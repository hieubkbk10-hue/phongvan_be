<?php

namespace App\Containers\AppSection\Product\UI\API\Requests;

use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

class CreateProductRequest extends ParentRequest
{
    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    protected array $decode = [
    ];

    protected array $urlParameters = [
    ];

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
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
            $allowed = ['name', 'price', 'status'];
            $inputKeys = array_keys($this->all());
            $extra = array_diff($inputKeys, $allowed);
            if (!empty($extra)) {
                $validator->errors()->add('fields', 'Unallowed fields present.');
            }
        });
    }
}
