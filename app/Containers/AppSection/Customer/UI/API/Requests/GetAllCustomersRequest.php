<?php

namespace App\Containers\AppSection\Customer\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

class GetAllCustomersRequest extends ParentRequest
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
            'page' => ['sometimes', 'integer', 'min:1'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'nullable', 'string'],
            'searchFields' => ['sometimes', 'nullable', 'string'],
            'searchJoin' => ['sometimes', 'nullable', 'string', Rule::in(['and', 'or', 'AND', 'OR'])],
            'orderBy' => ['sometimes', 'nullable', 'string'],
            'sortedBy' => ['sometimes', 'nullable', 'string', Rule::in(['asc', 'desc', 'ASC', 'DESC'])],
            'filter' => ['sometimes', 'nullable', 'string'],
            'include' => ['sometimes', 'nullable', 'string'],
            'trashed' => ['sometimes', 'nullable', 'string'],
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
            $allowed = ['page', 'limit', 'search', 'searchFields', 'searchJoin', 'orderBy', 'sortedBy', 'filter', 'include', 'trashed'];
            $inputKeys = array_keys($this->all());
            $extra = array_diff($inputKeys, $allowed);
            if (!empty($extra)) {
                $validator->errors()->add('fields', 'Unallowed parameters present.');
            }
        });
    }
}
