<?php

namespace App\Containers\AppSection\Media\UI\API\Requests;

use App\Containers\AppSection\Media\Models\Media;
use App\Ship\Parents\Requests\Request as ParentRequest;

class UploadMediaRequest extends ParentRequest
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
        'mediable_id',
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
            'file' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,jpg,gif,webp,svg',
                'max:10240', // Dung lượng tối đa 10MB
                /**
                 * @psalm-suppress UnusedClosureParam
                 */
                function ($attribute, $value, $fail) {
                    $mediableType = $this->input('mediable_type');
                    $mediableId = $this->input('mediable_id');

                    if ($mediableType && $mediableId) {
                        // Kiểm tra nếu loại đối tượng là Product thì không được vượt quá 9 ảnh
                        $isProduct = str_contains(strtolower((string) $mediableType), 'product');
                        if ($isProduct) {
                            $totalMedia = Media::query()
                                ->where('mediable_type', $mediableType)
                                ->where('mediable_id', $mediableId)
                                ->count();

                            if ($totalMedia >= 9) {
                                $fail('Upload Product không được vượt quá tổng 9 ảnh.');
                            }
                        }
                    }
                },
            ],
            'mediable_type' => 'nullable|string',
            'mediable_id' => 'nullable',
            'is_main' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'disk' => 'nullable|string',
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
