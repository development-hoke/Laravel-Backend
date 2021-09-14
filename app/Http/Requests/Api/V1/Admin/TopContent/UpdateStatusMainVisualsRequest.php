<?php

namespace App\Http\Requests\Api\V1\Admin\TopContent;

use App\Http\Requests\Api\V1\Request;

class UpdateStatusMainVisualsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|boolean',
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
            'status' => __('validation.attributes.top_content.status'),
        ];
    }
}
