<?php

namespace App\Http\Requests\Api\V1\Front\Item;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class EcDataRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'jan_code' => 'nullable|string',
            'offset' => 'nullable|integer',
            'limit' => 'nullable|integer',
        ];
    }

    public function attributes()
    {
        return [
            'jan_code' => __('validation.attributes.item_detail_identification.jan_code'),
        ];
    }
}
