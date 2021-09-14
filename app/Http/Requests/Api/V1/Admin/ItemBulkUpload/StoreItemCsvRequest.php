<?php

namespace App\Http\Requests\Api\V1\Admin\ItemBulkUpload;

use App\Http\Requests\Api\V1\Request;

class StoreItemCsvRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file_name' => 'required|string|max:255',
            'content' => sprintf('required|string|max:%s', config('fileupload.default_max_size.csv')),
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
            'file_name' => __('validation.attributes.item_bulk_upload.file_name'),
            'content' => __('validation.attributes.file_content'),
        ];
    }
}
