<?php

namespace App\Http\Requests\Api\V1\Admin\ContentImage;

use App\Http\Requests\Api\V1\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CreateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'upload' => 'required|file|image|max:5120',
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
            'upload' => __('validation.attributes.content_image.upload'),
        ];
    }

    /**
     * バリデーションに失敗した時の処理。
     * CKEditorのSimple upload adapterの実装に合わせる。
     * https://ckeditor.com/docs/ckeditor5/latest/features/image-upload/simple-upload-adapter.html#error-handling
     *
     * @param Validator $validator
     *
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $message = $validator->errors()->first('upload');

        throw new HttpResponseException(response([
            'error' => [
                'message' => $message,
            ],
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
