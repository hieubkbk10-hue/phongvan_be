<?php

namespace App\Containers\AppSection\Order\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

class DeleteOrderRequest extends ParentRequest
{
    protected array $access = [
        'permissions' => '',
        'roles' => '',
    ];

    public function rules(): array
    {
        return [
            'id' => ['prohibited'],
        ];
    }

    public function authorize(): bool
    {
        return false;
    }
}
