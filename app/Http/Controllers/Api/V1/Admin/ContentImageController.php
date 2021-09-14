<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\ContentImage\CreateRequest;
use App\Utils\FileUtil;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ContentImageController extends ApiAdminController
{
    /**
     * @param CreateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRequest $request)
    {
        try {
            $file = $request->file('upload');

            $path = sprintf(
                '%s/%s/%s/%s',
                config('filesystems.dirs.image.content'),
                Carbon::now()->format('YmdHis.u'),
                rand(0, 9),
                $file->getClientOriginalName()
            );

            $url = FileUtil::putPublicImage($path, $file->get());

            return response()->json(['url' => $url], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            report($e);

            // CKEditorのSimple upload adapterの実装に合わせる
            // https://ckeditor.com/docs/ckeditor5/latest/features/image-upload/simple-upload-adapter.html#error-handling
            throw new HttpResponseException(response([
                'error' => [
                    'message' => __('error.failed_to_upload_file'),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }
}
