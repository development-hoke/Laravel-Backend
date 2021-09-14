<?php

namespace App\Http\Requests\Api\V1\Admin\PastItem;

use App\Http\Requests\Api\V1\Request;

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
            'product_number.*' => 'string|max:255',
            'maker_product_number.*' => 'string|max:255',
            'name' => 'string|max:255',
            'page' => 'nullable|integer',
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
            'organization_id.*' => __('validation.attributes.past_item.organization_id'),
            'division_id.*' => __('validation.attributes.past_item.division_id'),
            'department_id.*' => __('validation.attributes.past_item.department_id'),
            'product_number.*' => __('validation.attributes.past_item.product_number'),
            'maker_product_number.*' => __('validation.attributes.past_item.maker_product_number'),
            'name' => __('validation.attributes.past_item.name'),
            'page' => __('validation.page'),
        ];
    }
}
