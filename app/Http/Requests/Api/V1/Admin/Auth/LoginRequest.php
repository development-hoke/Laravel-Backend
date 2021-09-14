<?php

namespace App\Http\Requests\Api\V1\Admin\Auth;

use App\Http\Requests\Api\V1\Request;

class LoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'code' => __('validation.attributes.admin_auth.code'),
            'password' => __('validation.attributes.admin_auth.passowrd'),
        ];
    }
}
