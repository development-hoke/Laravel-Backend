<?php

namespace App\Http\Requests\Api\V1\Admin\TopContent;

use App\Http\Requests\Api\V1\Request;

class UpdateMainVisualRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_index' => 'required|integer',
            'new_sort' => 'required|integer',
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
            'old_index' => __('validation.attributes.top_content.sort.old_index'),
            'new_sort' => __('validation.attributes.top_content.sort.new_sort'),
        ];
    }
}
