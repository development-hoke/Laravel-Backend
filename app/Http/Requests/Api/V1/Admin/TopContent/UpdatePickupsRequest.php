<?php

namespace App\Http\Requests\Api\V1\Admin\TopContent;

use App\Http\Requests\Api\V1\Request;

class UpdatePickupsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sort' => 'required|integer',
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
            'sort' => __('validation.attributes.sort'),
        ];
    }
}
