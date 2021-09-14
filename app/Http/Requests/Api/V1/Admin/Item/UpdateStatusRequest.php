<?php

namespace App\Http\Requests\Api\V1\Admin\Item;

use App\Http\Requests\Api\V1\Request;

class UpdateStatusRequest extends Request
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
            'status' => __('validation.attributes.item.status'),
        ];
    }
}
