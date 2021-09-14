<?php

namespace App\Http\Requests\Api\V1\Front\Auth;

use App\Http\Requests\Api\V1\Front\BaseMemberRequest;

class ChangePasswordRequest extends BaseMemberRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
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
            'password' => __('validation.attributes.change_password.password'),
            'new_password' => __('validation.attributes.change_password.new_password'),
        ] + parent::attributes();
    }
}
