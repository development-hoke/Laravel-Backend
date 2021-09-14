<?php

namespace App\Http\Requests\Api\V1\Admin\SalesAggregation;

use App\Criteria\SalesAggregation\AdminSortItemCriteria;
use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

class AggregateItemsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date_from' => 'required_without:use_default|date',
            'date_to' => 'required_without:use_default|date',
            'by' => ['required_without:use_default', Rule::in(\App\Enums\OrderAggregation\By::getValues())],
            'sale_type' => ['required_without:use_default', Rule::in(\App\Enums\Order\SaleType::getValues())],
            'organization_id.*' => 'integer|exists:organizations,id',
            'division_id.*' => 'integer|exists:divisions,id',
            'main_store_brand.*' => [Rule::in(\App\Enums\Common\StoreBrand::getValues())],
            'department_id.*' => 'integer|exists:departments,id',
            'online_category_id.*' => 'integer|exists:online_categories,id',
            'product_number.*' => 'string|max:255',
            'maker_product_number.*' => 'string|max:255',
            'use_default' => 'boolean',
            'page' => 'nullable|integer',
            'sort' => ['nullable', Rule::in(AdminSortItemCriteria::getSortOptions())],
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
            'date_from' => __('validation.attributes.sales_aggregation_order.date_from'),
            'date_to' => __('validation.attributes.sales_aggregation_order.date_to'),
            'by' => __('validation.attributes.sales_aggregation_order.by'),
            'organization_id.*' => __('validation.attributes.item.organization_id'),
            'division_id.*' => __('validation.attributes.item.division_id'),
            'main_store_brand.*' => __('validation.attributes.item.main_store_brand'),
            'department_id.*' => __('validation.attributes.item.department_id'),
            'online_category_id.*' => __('validation.attributes.online_category_id'),
            'product_number.*' => __('validation.attributes.item.product_number'),
            'maker_product_number.*' => __('validation.attributes.item.maker_product_number'),
            'page' => __('validation.attributes.page'),
            'sort' => __('validation.attributes.sort'),
        ];
    }

    /**
     * デフォルトのパラメータ
     *
     * @return array
     */
    public function getDefaultParams()
    {
        return [
            'date_from' => date('Y-m-d 00:00:00', strtotime('-1 month')),
            'date_to' => date('Y-m-d 00:00:00'),
            'by' => \App\Enums\OrderAggregation\By::Ordered,
            'unit' => \App\Enums\OrderAggregation\Unit::Daily,
            'sale_type' => \App\Enums\Order\SaleType::Sale,
            'sort' => AdminSortItemCriteria::buildQueryParam('total_amount', 'desc'),
        ];
    }
}
