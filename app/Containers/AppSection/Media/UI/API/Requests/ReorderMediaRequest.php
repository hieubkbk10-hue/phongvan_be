<?php

namespace App\Containers\AppSection\Media\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

class ReorderMediaRequest extends ParentRequest
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
        'medias.*.id',
    ];

    /**
     * Defining the URL parameters allows applying validation rules on them.
     */
    protected array $urlParameters = [
    ];

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'medias' => 'required|array|min:1|max:9',
            'medias.*.id' => 'required',
            'medias.*.sort_order' => 'required|integer|min:0',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'medias.max' => 'Reorder chỉ nhận tối đa 9 Media ID.',
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
}
