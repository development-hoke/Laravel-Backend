<?php

namespace App\Http\Requests\Api\V1\Front\Item;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class ArriveBackOrderedRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            '*.item_id' => 'required|exists:order_details,item_detail_id,deleted_at,NULL',
            '*.amount' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            '*.item_id' => __('validation.attributes.items.item_id'),
            '*.amount' => __('validation.attributes.items.amount'),
        ];
    }
}
