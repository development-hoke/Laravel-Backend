<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\UrgentNotice as UrgentNoticeResource;
use App\Repositories\UrgentNoticeRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UrgentNoticeController extends Controller
{
    /**
     * @var UrgentNoticeRepository
     */
    protected $urgentNoticeRepository;

    public function __construct(UrgentNoticeRepository $urgentNoticeRepository)
    {
        $this->urgentNoticeRepository = $urgentNoticeRepository;
    }

    /**
     * @param Request $request
     *
     * @return UrgentNoticeResource
     */
    public function index(Request $request)
    {
        try {
            $urgentNotice = $this->urgentNoticeRepository->first();
            if ($urgentNotice->status) {
                return new UrgentNoticeResource($urgentNotice);
            } else {
                return null;
            }
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found'), $e);
        }
    }
}
