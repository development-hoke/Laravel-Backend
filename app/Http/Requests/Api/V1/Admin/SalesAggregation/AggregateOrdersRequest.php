<?php

namespace App\Http\Requests\Api\V1\Admin\SalesAggregation;

use App\Enums\OrderAggregation\By as AggregationBy;
use App\Enums\OrderAggregation\Group1;
use App\Enums\OrderAggregation\Group2;
use App\Enums\OrderAggregation\Unit as AggregationUnit;
use App\Http\Requests\Api\V1\Request;
use App\Models\Department;
use App\Models\Organization;
use Illuminate\Validation\Rule;

class AggregateOrdersRequest extends Request
{
    /**
     * ページ上限
     */
    const MAX_PER_PAGE = 500;

    /**
     * デフォルトのパラメータに指定するorganization_id取得時のlimitの値
     */
    const DEFAULT_DEPARTMENT_LIMIT = 5;

    /**
     * デフォルトのパラメータに指定するdepartment_id取得時のlimitの値
     */
    const DEFAULT_ORGANIZATION_LIMIT = 5;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date_from' => 'required_if:use_default,0|required_without:use_default|date',
            'date_to' => 'required_if:use_default,0|required_without:use_default|date',
            'by' => ['required_if:use_default,0', 'required_without:use_default', Rule::in(AggregationBy::getValues())],
            'unit' => ['required_if:use_default,0', 'required_without:use_default', Rule::in(AggregationUnit::getValues())],
            'group1' => ['required_if:use_default,0', 'required_without:use_default', Rule::in(Group1::getValues())],
            'organization_id' => ['array', Rule::requiredIf(function () {
                return !((int) $this->use_default) && $this->group1 === Group1::Organization;
            })],
            'organization_id.*' => 'integer|exists:organizations,id',
            'division_id' => ['array', Rule::requiredIf(function () {
                return !((int) $this->use_default) && $this->group1 === Group1::Division;
            })],
            'division_id.*' => 'integer|exists:divisions,id',
            'main_store_brand' => ['array', Rule::requiredIf(function () {
                return !((int) $this->use_default) && $this->group1 === Group1::MainStoreBrand;
            })],
            'main_store_brand.*' => [Rule::in(\App\Enums\Common\StoreBrand::getValues())],
            'group2' => ['required_if:use_default,0', 'required_without:use_default', Rule::in(Group2::getValues())],
            'department_id' => ['array', Rule::requiredIf(function () {
                return !((int) $this->use_default) && $this->group2 === Group2::Department;
            })],
            'department_id.*' => 'integer|exists:departments,id',
            'online_category_id' => ['array', Rule::requiredIf(function () {
                return !((int) $this->use_default) && $this->group2 === Group2::OnlineCategory;
            })],
            'online_category_id.*' => 'integer|exists:online_categories,id',
            'product_number.*' => 'string|max:255',
            'maker_product_number.*' => 'string|max:255',
            'use_default' => 'boolean',
            'page' => 'integer',
            'per_page' => ['integer', sprintf('max:%d', self::MAX_PER_PAGE)],
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
            'unit' => __('validation.attributes.sales_aggregation_order.unit'),
            'group1' => __('validation.attributes.sales_aggregation_order.group1'),
            'group2' => __('validation.attributes.sales_aggregation_order.group2'),
            'organization_id' => __('validation.attributes.item.organization_id'),
            'organization_id.*' => __('validation.attributes.item.organization_id'),
            'division_id' => __('validation.attributes.item.division_id'),
            'division_id.*' => __('validation.attributes.item.division_id'),
            'main_store_brand' => __('validation.attributes.item.main_store_brand'),
            'main_store_brand.*' => __('validation.attributes.item.main_store_brand'),
            'department_id' => __('validation.attributes.item.department_id'),
            'department_id.*' => __('validation.attributes.item.department_id'),
            'online_category_id' => __('validation.attributes.online_category_id'),
            'online_category_id.*' => __('validation.attributes.online_category_id'),
            'product_number.*' => __('validation.attributes.item.product_number'),
            'maker_product_number.*' => __('validation.attributes.item.maker_product_number'),
            'page' => __('validation.attributes.page'),
            'per_page' => __('validation.attributes.per_page'),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'required_without' => __('validation.required'),
            'required_if' => __('validation.required'),
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
            'by' => AggregationBy::Ordered,
            'unit' => AggregationUnit::Daily,
            'group1' => Group1::Organization,
            'organization_id' => Organization::limit(self::DEFAULT_ORGANIZATION_LIMIT)->get()->pluck('id')->toArray(),
            'group2' => Group2::Department,
            'department_id' => Department::limit(self::DEFAULT_DEPARTMENT_LIMIT)->get()->pluck('id')->toArray(),
        ];
    }
}
