<?php

namespace App\Http\Requests\Api\V1\Front\Contact;

use App\Enums\Contact\Type as ContactType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'type' => [
                'required',
                Rule::in(ContactType::getValues()),
            ],
            'content' => 'required|string|max:3000',
            'lastName' => 'required|string',
            'firstName' => 'required|string',
            'lastNameKana' => 'required|string',
            'firstNameKana' => 'required|string',
            'email' => 'required|email|confirmed',
            'email_confirmation' => 'required|email',
            'phone' => 'required|string',
        ];
    }
}
