<?php

namespace App\Ship\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsBase64Image implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        private string $name)
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^data:image\/(?:gif|png|jpeg|bmp|webp)(?:;charset=utf-8)?;base64,.+/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.base64_image', ['attribute' => $this->name]);
    }
}
