<?php

namespace App\Validation\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

/**
 * お届け希望日として許可されている日にちであるか判定
 */
class AllowedDeliveryDate implements Rule
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
        $date = new Carbon($value);
        $allowedToDate = Carbon::now()->addDays(config('constants.order.delivery_date.to') - 1);
        $allowedFromDate = Carbon::now()->addDays(config('constants.order.delivery_date.from') + 1);

        return $date->between($allowedToDate, $allowedFromDate);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.allowed_date');
    }
}
