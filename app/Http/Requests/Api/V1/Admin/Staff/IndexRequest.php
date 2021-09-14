<?php

namespace App\Http\Requests\Api\V1\Admin\Staff;

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
            'id' => 'array',
            'id.*' => 'integer',
            'name' => 'nullable|string|max:255',
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
            'name' => __('validation.attributes.staff.name'),
        ];
    }
}
