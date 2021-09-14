<?php

namespace App\Http\Requests\Api\V1\Admin\Help;

use App\Criteria\Help\AdminSortCriteria;
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
            'page' => __('validation.page'),
        ];
    }
}
