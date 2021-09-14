<?php

namespace App\Http\Requests\Api\V1\Admin\Color;

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
            'page' => 'nullable|integer',
            'all' => 'nullable|boolean',
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
