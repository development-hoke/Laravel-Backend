<?php

namespace App\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 郵便番号判定
 */
class Postal implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = replace_hyphen($value);

        return preg_match('/^\d{3}-\d{4}$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.postal');
    }
}
