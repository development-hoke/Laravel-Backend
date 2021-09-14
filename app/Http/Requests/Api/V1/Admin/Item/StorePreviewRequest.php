<?php

namespace App\Http\Requests\Api\V1\Admin\Item;

class StorePreviewRequest extends UpdateRequest
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            'status' => 'boolean',
        ]);
    }
}
