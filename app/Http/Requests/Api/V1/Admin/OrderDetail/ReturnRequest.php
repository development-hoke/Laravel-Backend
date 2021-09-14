<?php

namespace App\Http\Requests\Api\V1\Admin\OrderDetail;

use App\Http\Requests\Api\V1\Request;

class ReturnRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids' => 'array|required',
            'ids.*' => 'required|integer|exists:order_details,id,deleted_at,NULL',
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
            'ids' => __('validation.attributes.order_detail.id'),
            'ids.*' => __('validation.attributes.order_detail.id'),
        ];
    }
}
