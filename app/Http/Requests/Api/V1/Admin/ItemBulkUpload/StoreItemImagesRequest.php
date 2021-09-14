<?php

namespace App\Http\Requests\Api\V1\Admin\ItemBulkUpload;

use App\Http\Requests\Api\V1\Request;

class StoreItemImagesRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|file|max:1024',
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
            'content' => __('validation.attributes.file_content'),
        ];
    }
}
