<?php

namespace App\Containers\AppSection\Customer\UI\API\Requests;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends ParentRequest
{
    /**
     * Define which Roles and/or Permissions has access to this request.
     */
    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    /**
     * Id's that needs decoding before applying the validation rules.
     */
    protected array $decode = [
        'id',
    ];

    /**
     * Defining the URL parameters (e.g, `/user/{id}`) allows applying
     * validation rules on them and allows accessing them like request data.
     */
    protected array $urlParameters = [
        'id',
    ];

    protected function prepareForValidation(): void
    {
        if ($this->has('phone') && is_string($this->phone)) {
            $this->merge([
                'phone' => str_replace([' ', '-', '(', ')'], '', $this->phone),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', Rule::exists(Customer::getTableName(), 'id')->whereNull('deleted_at')],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'phone' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^\+[1-9][0-9]{7,14}$/', Rule::unique(Customer::getTableName(), 'phone')->ignore($this->id)],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:150'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->check([
            'hasAccess',
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $allowed = ['id', 'name', 'phone', 'address', 'email'];
            $inputKeys = array_keys($this->all());
            $extra = array_diff($inputKeys, $allowed);
            if (!empty($extra)) {
                $validator->errors()->add('fields', 'Unallowed fields present.');
            }
        });
    }
}
