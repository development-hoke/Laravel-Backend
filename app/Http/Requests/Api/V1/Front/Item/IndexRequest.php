<?php

namespace App\Http\Requests\Api\V1\Front\Item;

use App\Http\Requests\Api\V1\Front\BaseRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'q' => 'max:10000',
            'main_store_brand.*' => [Rule::in(\App\Enums\Common\StoreBrand::getValues())],
            'brand_id.*' => 'integer|exists:brands,id',
            'online_category_id.*' => 'integer|exists:online_categories,id',
            'sales_type_id.*' => 'integer|required|exists:sales_types,id',
            'color_id.*' => 'integer|exists:colors,id',
            'page' => 'integer',
            'sort' => [Rule::in(\App\Criteria\Item\FrontSortCriteria::getSortOptions())],
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
            'q' => __('validation.attributes.item.q'),
            'main_store_brand.*' => __('validation.attributes.item.main_store_brand'),
            'online_category_id.*' => __('validation.attributes.online_category_id'),
            'sales_type_id.*' => __('resource.sales_type'),
            'color_id.*' => __('validation.attributes.item_image.color_id'),
            'brand_id.*' => __('validation.attributes.item.brand_id'),
            'page' => __('validation.attributes.page'),
        ];
    }
}
