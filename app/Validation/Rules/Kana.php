<?php

namespace App\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * カタカナ判定
 */
class Kana implements Rule
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
        return (bool) preg_match('/^[ァ-ヶー　]+$/u', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.kana');
    }
}
