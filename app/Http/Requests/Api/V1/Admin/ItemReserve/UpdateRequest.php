<?php

namespace App\Http\Requests\Api\V1\Admin\ItemReserve;

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
            'is_enable' => 'required|boolean',
            'period_from' => "required|date|before:{$this->period_to}",
            'period_to' => 'required|date',
            'reserve_price' => 'required|integer',
            'is_free_delivery' => 'required|boolean',
            'limited_stock_threshold' => 'required|integer',
            'out_of_stock_threshold' => 'required|integer',
            'expected_arrival_date' => 'required|max:255',
            'note' => 'required|max:10000',
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
            'is_enable' => __('validation.attributes.item_reserve.is_enable'),
            'period_from' => __('validation.attributes.item_reserve.period_from'),
            'period_to' => __('validation.attributes.item_reserve.period_to'),
            'reserve_price' => __('validation.attributes.item_reserve.reserve_price'),
            'is_free_delivery' => __('validation.attributes.item_reserve.is_free_delivery'),
            'limited_stock_threshold' => __('validation.attributes.item_reserve.limited_stock_threshold'),
            'out_of_stock_threshold' => __('validation.attributes.item_reserve.out_of_stock_threshold'),
            'expected_arrival_date' => __('validation.attributes.item_reserve.expected_arrival_date'),
            'note' => __('validation.attributes.item_reserve.note'),
        ];
    }
}
