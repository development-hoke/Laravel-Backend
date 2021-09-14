<?php

namespace App\Http\Requests\Api\V1\Admin\ItemDetail;

use App\Criteria\ItemDetail\AdminSortCriteria;
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
            'organization_id.*' => 'nullable|exists:organizations,id',
            'division_id.*' => 'nullable|integer|exists:divisions,id',
            'department_id.*' => 'nullable|integer|exists:departments,id',
            'item_id' => 'nullable|integer',
            'term_id.*' => 'nullable|integer|exists:terms,id',
            'fashion_speed.*' => ['nullable', Rule::in(\App\Enums\Item\FashionSpeed::getValues())],
            'product_number.*' => 'nullable|string|max:255',
            'maker_product_number.*' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(\App\Enums\Common\Status::getValues())],
            'stock_type' => ['nullable', Rule::in(\App\Enums\Params\Item\Stock::getValues())],
            'has_stock' => 'nullable|boolean',
            'dead_inventory_day_type' => ['nullable', Rule::in(\App\Enums\ItemDetail\DeadInventoryDayType::getValues())],
            'slow_moving_inventory_day_type' => ['nullable', Rule::in(\App\Enums\ItemDetail\SlowMovingInventoryDayType::getValues())],
            'last_sales_date_from' => 'nullable|date',
            'last_sales_date_to' => 'nullable|date',
            'containing_sales_status_stop' => 'nullable|boolean',
            'containing_sales_status_sold_out' => 'nullable|boolean',
            'page' => 'nullable|integer',
            'sort' => ['nullable', Rule::in(AdminSortCriteria::getSortOptions())],
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
            'term_id' => __('validation.attributes.item.term_id'),
            'organization_id' => __('validation.attributes.item.organization_id'),
            'division_id' => __('validation.attributes.item.division_id'),
            'department_id' => __('validation.attributes.item.department_id'),
            'product_number' => __('validation.attributes.item.product_number'),
            'maker_product_number' => __('validation.attributes.item.maker_product_number'),
            'fashion_speed' => __('validation.attributes.item.fashion_speed'),
            'status' => __('validation.attributes.item.status'),
            'stock_type' => __('validation.attributes.stock'),
            'has_stock' => __('validation.attributes.item_detail.has_stock'),
            'dead_inventory_day_type' => __('validation.attributes.dead_inventory_days'),
            'slow_moving_inventory_day_type' => __('validation.attributes.slow_moving_inventory_days'),
            'last_sales_date_from' => __('validation.attributes.last_sales_date'),
            'last_sales_date_to' => __('validation.attributes.last_sales_date'),
            'containing_sales_status_stop' => __('validation.attributes.containing_sales_status_stop'),
            'containing_sales_status_sold_out' => __('validation.attributes.containing_sales_status_sold_out'),
            'page' => __('validation.page'),
        ];
    }
}
