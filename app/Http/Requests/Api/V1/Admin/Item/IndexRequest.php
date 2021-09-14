<?php

namespace App\Http\Requests\Api\V1\Admin\Item;

use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'organization_id.*' => 'exists:organizations,id',
            'division_id.*' => 'integer|exists:divisions,id',
            'department_id.*' => 'integer|exists:departments,id',
            'term_id.*' => 'integer|exists:terms,id',
            'fashion_speed.*' => [Rule::in(\App\Enums\Item\FashionSpeed::getValues())],
            'status' => [Rule::in(\App\Enums\Common\Status::getValues())],
            'main_store_brand.*' => [Rule::in(\App\Enums\Common\StoreBrand::getValues())],
            'online_category_id.*' => 'integer|exists:online_categories,id',
            'online_tag_id.*' => 'integer|exists:online_tags,id',
            'stock_type' => [Rule::in(\App\Enums\Params\Item\Stock::getValues())],
            'favorite_count' => 'integer',
            'product_number.*' => 'string|max:255',
            'maker_product_number.*' => 'string|max:255',
            'name' => 'string|max:255',
            'page' => 'nullable|integer',
            'sale_stop' => 'string|max:10',
            'sale_sold_out' => 'string|max:10',
            'old_jan_code' => 'string|max:13',
            'jan_code' => 'string|max:30',
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
            'term_id.*' => __('validation.attributes.item.term_id'),
            'organization_id.*' => __('validation.attributes.item.organization_id'),
            'division_id.*' => __('validation.attributes.item.division_id'),
            'department_id.*' => __('validation.attributes.item.department_id'),
            'product_number.*' => __('validation.attributes.item.product_number'),
            'maker_product_number.*' => __('validation.attributes.item.maker_product_number'),
            'fashion_speed' => __('validation.attributes.item.fashion_speed'),
            'name' => __('validation.attributes.item.name'),
            'status' => __('validation.attributes.item.status'),
            'main_store_brand.*' => __('validation.attributes.item.main_store_brand'),
            'online_category_id.*' => __('validation.attributes.online_category_id'),
            'online_tag_id.*' => __('validation.attributes.online_tag_id'),
            'favorite_count' => __('validation.attributes.favorite_count'),
            'stock_type' => __('validation.attributes.stock'),
            'page' => __('validation.page'),
            'sale_stop' => __('validation.sale_stop'),
            'sale_sold_out' => __('validation.sale_sold_out'),
        ];
    }
}
