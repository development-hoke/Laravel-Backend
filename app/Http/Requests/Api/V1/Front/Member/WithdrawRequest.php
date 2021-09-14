<?php

namespace App\Http\Requests\Api\V1\Front\Member;

use App\Http\Requests\Api\V1\Front\BaseMemberRequest;

class WithdrawRequest extends BaseMemberRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason' => 'required|numeric',
        ] + parent::rules();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'reason' => __('validation.attributes.withdraw.reason'),
        ] + parent::attributes();
    }
}
