<?php

namespace App\Http\Requests\Api\V1\Admin\AdminLog;

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
            'term_from' => array_merge(
                ['nullable', 'date'],
                !empty($this->term_to) ? ['before:' . $this->term_to] : []
            ),
            'term_to' => 'nullable|date',
            'staff_id' => 'array',
            'staff_id.*' => 'integer|exists:staffs,id',
            'action' => 'array',
            'action.*' => 'string|max:255',
            'page' => 'nullable|integer',
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
            'term_from' => __('validation.attributes.admin_log.term_from'),
            'term_to' => __('validation.attributes.admin_log.term_to'),
            'action' => __('validation.attributes.admin_log.action'),
            'staff_name' => __('validation.attributes.staff.name'),
            'page' => __('validation.attributes.page'),
        ];
    }
}
