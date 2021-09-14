<?php

namespace App\Http\Requests\Api\V1\Admin\TopContent;

use App\Http\Requests\Api\V1\Request;

class AddMainVisualsRequest extends Request
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
            'url' => 'required|url|max:255',
            'pc_path' => 'array',
            'pc_path.file_name' => 'nullable|max:255',
            'pc_path.raw_image' => sprintf(
                'required|max:%s',
                config('fileupload.default_max_size.main_visual')
            ),
            'sp_path' => 'array',
            'sp_path.file_name' => 'nullable|max:255',
            'sp_path.raw_image' => sprintf(
                'required|max:%s',
                config('fileupload.default_max_size.main_visual')
            ),
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
            'status' => __('validation.attributes.top_content.main_visual.status'),
            'url' => __('validation.attributes.top_content.main_visual.url'),
            'pc_path' => __('validation.attributes.top_content.main_visual.pc_path'),
            'pc_path.file_name' => __('validation.attributes.top_content.main_visual.pc_path_file_name'),
            'pc_path.raw_image' => __('validation.attributes.top_content.main_visual.pc_path_raw_image'),
            'sp_path' => __('validation.attributes.top_content.sp_path'),
            'sp_path.file_name' => __('validation.attributes.top_content.main_visual.sp_path_file_name'),
            'sp_path.raw_image' => __('validation.attributes.top_content.main_visual.sp_path_raw_image'),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            '*.raw_image.max' => __('validation.top_content.raw_image_max'),
        ];
    }
}
