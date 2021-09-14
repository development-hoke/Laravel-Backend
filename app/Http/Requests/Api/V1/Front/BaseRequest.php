<?php

namespace App\Http\Requests\Api\V1\Front;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
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
}
