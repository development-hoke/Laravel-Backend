<?php

namespace App\Validation\Rules;

class LessThenOrEqualWithTable extends ComparedWithTable
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
        return $value <= $this->comparedValue;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return str_replace(
            ':field',
            $this->fieldName,
            __('validation.less_then_or_equal_with_table')
        );
    }
}
