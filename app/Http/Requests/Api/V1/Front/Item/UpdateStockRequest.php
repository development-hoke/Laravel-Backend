<?php

namespace App\Http\Requests\Api\V1\Front\Item;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class UpdateStockRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'item_jan_id' => 'required',
            'ec_stock' => 'required|numeric',
            'reservable_stock' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'item_jan_id' => __('validation.attributes.items.item_id'),
            'ec_stock' => __('validation.attributes.items.amount'),
            'reservable_stock' => __('validation.attributes.items.amount'),
        ];
    }
}
