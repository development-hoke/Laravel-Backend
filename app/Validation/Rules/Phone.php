<?php

namespace App\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 電話番号判定
 */
class Phone implements Rule
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

        return preg_match('/\A\d{2,4}+-\d{2,4}+-\d{4}\z/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.phone');
    }
}
