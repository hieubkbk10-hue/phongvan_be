<?php

namespace App\Containers\AppSection\Product\UI\API\Requests;

use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

class FindProductByIdRequest extends ParentRequest
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
            'include' => ['sometimes', 'nullable', 'string'],
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
            $allowed = ['id', 'include'];
            $inputKeys = array_keys($this->all());
            $extra = array_diff($inputKeys, $allowed);
            if (!empty($extra)) {
                $validator->errors()->add('fields', 'Unallowed parameters present.');
            }
        });
    }
}
