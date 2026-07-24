<?php

namespace App\Containers\AppSection\Media\UI\API\Requests;

use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

class UploadMediaRequest extends ParentRequest
{
    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    protected array $decode = [
        'product_id',
    ];

    protected array $urlParameters = [
        'product_id',
    ];

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', Rule::exists(Product::getTableName(), 'id')],
            'file' => ['required', 'file', 'image', 'max:10240'],
            'is_main' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
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
            $allowed = ['product_id', 'file', 'is_main', 'sort_order'];
            $inputKeys = array_keys($this->all());
            $extra = array_diff($inputKeys, $allowed);
            if (!empty($extra)) {
                $validator->errors()->add('fields', 'Unallowed parameters present.');
            }
        });
    }
}
