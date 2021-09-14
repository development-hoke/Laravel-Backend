<?php

namespace App\Http\Requests\Api\V1\Admin\EventUser;

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
            'event_id' => 'required|integer',
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
            'event_id' => __('validation.attributes.event.id'),
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
