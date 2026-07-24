<?php

namespace App\Containers\AppSection\Media\UI\API\Requests;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

class SetPrimaryMediaRequest extends ParentRequest
{
    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    protected array $decode = [
        'product_id',
        'id',
    ];

    protected array $urlParameters = [
        'product_id',
        'id',
    ];

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', Rule::exists(Product::getTableName(), 'id')],
            'id' => ['required', 'integer', Rule::exists(Media::getTableName(), 'id')],
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
            $allowed = ['product_id', 'id'];
            $inputKeys = array_keys($this->all());
            $extra = array_diff($inputKeys, $allowed);
            if (!empty($extra)) {
                $validator->errors()->add('fields', 'Unallowed parameters present.');
            }
        });
    }
}
