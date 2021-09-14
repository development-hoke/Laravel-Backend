<?php

namespace App\Http\Requests\Api\V1\Admin\OrderDetail;

use App\Http\Requests\Api\V1\Request;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'items' => 'array|required',
            'items.*.item_detail_id' => 'required|integer|exists:item_details,id,deleted_at,NULL',
            'items.*.amount' => 'required|integer',
            'items.*.price' => 'required|integer',
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
            'items.*.item_detail_id' => __('validation.attributes.order_detail.item_detail_id'),
            'items.*.amount' => __('validation.attributes.order_detail.amount'),
            'items.*.price' => __('validation.attributes.order_detail.price'),
        ];
    }
}
