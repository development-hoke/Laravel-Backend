<?php

namespace App\Http\Requests\Api\V1\Front\Member;

use App\Http\Requests\Api\V1\Front\BaseMemberRequest;

class UpdateMailDmRequest extends BaseMemberRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mail_dm' => 'required|integer',
            'post_dm' => 'required|integer',
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
            'mail_dm' => __('validation.attributes.destination.mail_dm'),
            'post_dm' => __('validation.attributes.destination.post_dm'),
        ] + parent::attributes();
    }
}
