<?php

namespace App\Ship\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueColumns implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        private string $table,
        private array $columns,
        private int $ignoreId = 0,
        private string $ignoreColumn = 'id')
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
        $db = DB::table($this->table);
        foreach ($this->columns as $column => $value) {
            $db->where($column, $value);
        }

        if ($this->ignoreId) {
            $db->where($this->ignoreColumn, '!=', $this->ignoreId);
        }

        if ($db->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.unique', ['attribute' => implode(', ', array_keys($this->columns))]);
    }
}
