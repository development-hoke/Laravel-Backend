<?php

namespace App\Http\Requests\Api\V1\Admin\EventItem;

use App\Http\Requests\Api\V1\Request;

class StoreCsvRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'event_id' => 'required|integer',
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
            'event_id' => __('validation.attributes.event_item.event_id'),
            'content' => __('validation.attributes.file_content'),
        ];
    }

    /**
     * ルート引数は対象にならないのでマージする
     * DEPRECATED: この方法は今後使用しない。(参照: https://github.com/u2ku2k/store.ymdy/pull/260)
     *
     * @return array
     */
    public function validationData()
    {
        return array_merge($this->request->all(), [
            'event_id' => $this->route('event_id'),
        ]);
    }
}
