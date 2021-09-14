<?php

namespace App\Http\Requests\Api\V1\Admin\Information;

class StorePreviewRequest extends UpdateRequest
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            'status' => 'boolean',
        ]);
    }
}
