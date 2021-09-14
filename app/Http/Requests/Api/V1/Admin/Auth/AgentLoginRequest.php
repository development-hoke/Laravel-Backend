<?php

namespace App\Http\Requests\Api\V1\Admin\Auth;

use App\Http\Requests\Api\V1\Request;

class AgentLoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'member_id' => 'required|integer',
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
            'member_id' => __('validation.attributes.member.id'),
        ];
    }
}
