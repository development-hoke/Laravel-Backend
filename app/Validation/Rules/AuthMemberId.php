<?php

namespace App\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 会員IDと認証情報の整合性チェック
 */
class AuthMemberId implements Rule
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
        return (int) $value === (int) auth('api')->id();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.invalid_member_id');
    }
}
