<?php

namespace App\Http\Requests\Api\V1\Admin\OrderDetail;

use App\Criteria\ItemDetail\AdminSortCriteria;
use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

class IndexItemsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'department_id' => 'nullable|integer|exists:departments,id',
            'maker_product_number' => 'nullable|string|max:255',
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
            'department_id' => __('validation.attributes.item.department_id'),
            'maker_product_number' => __('validation.attributes.item.maker_product_number'),
            'page' => __('validation.page'),
        ];
    }
}
