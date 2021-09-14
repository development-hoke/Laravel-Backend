<?php

namespace App\Http\Requests\Api\V1\Admin\Item;

use App\Http\Requests\Api\V1\Request;

class UploadImageRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => 'required|mimes:jpeg,jpg,png,gif',
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
            'image' => __('validation.attributes.item.image'),
        ];
    }
}
