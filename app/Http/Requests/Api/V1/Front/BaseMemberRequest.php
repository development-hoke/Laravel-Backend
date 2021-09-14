<?php

namespace App\Http\Requests\Api\V1\Front;

use App\Validation\Rules\AuthMemberId;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'member_id' => [
                'sometimes',
                'numeric',
                new AuthMemberId(),
            ],
        ];
    }

    /**
     * パスパラメータも追加
     *
     * @return array|null
     */
    public function validationData()
    {
        return $this->all() + $this->route()->parameters();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'member_id' => __('validation.attributes.member.id'),
        ];
    }
}
