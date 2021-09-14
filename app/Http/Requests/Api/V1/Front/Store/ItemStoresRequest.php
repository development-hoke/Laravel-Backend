<?php

namespace App\Http\Requests\Api\V1\Front\Store;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class ItemStoresRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'near_loc_lon' => [
                'required_with:near_loc_lat',
                'numeric',
                'between:'.implode(',', \App\Utils\Geometry::getLongitudeRange()),
            ],
            'near_loc_lat' => [
                'required_with:near_loc_lon',
                'numeric',
                'between:'.implode(',', \App\Utils\Geometry::getLatitudeRange()),
            ],
            'q' => 'string|max:500',
            'has_stock' => 'boolean',
            'page' => 'integer',
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
            'near_loc_lon' => __('validation.attributes.store.longitude'),
            'near_loc_lat' => __('validation.attributes.store.latitude'),
            'q' => __('validation.attributes.store.q'),
            'has_stock' => __('validation.attributes.store.has_stock'),
            'page' => __('validation.attributes.page'),
        ];
    }
}
