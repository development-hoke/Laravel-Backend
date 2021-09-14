<?php

namespace App\Http\Requests\Api\V1\Admin\UrgentNotice;

use App\Http\Requests\Api\V1\Request;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => 'required|max:10000',
            'status' => 'required|boolean',
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
            'body' => __('validation.attributes.urgent_notice.body'),
            'status' => __('validation.attributes.urgent_notice.status'),
        ];
    }
}
