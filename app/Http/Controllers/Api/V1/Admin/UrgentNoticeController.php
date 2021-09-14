<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\UrgentNotice\ShowRequest;
use App\Http\Requests\Api\V1\Admin\UrgentNotice\UpdateRequest;
use App\Http\Resources\UrgentNotice as UrgentNoticeResource;
use App\Repositories\UrgentNoticeRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UrgentNoticeController extends ApiAdminController
{
    /**
     * @var UrgentNoticeRepository
     */
    private $repository;

    /**
     * @param UrgentNoticeRepository $repository
     */
    public function __construct(UrgentNoticeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ShowRequest $request
     * @param int $id
     *
     * @return UrgentNoticeResource
     */
    public function show(ShowRequest $request): UrgentNoticeResource
    {
        try {
            $urgentNotice = $this->repository->first();

            return new UrgentNoticeResource($urgentNotice);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found'), $e);
        }
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return UrgentNoticeResource
     */
    public function update(UpdateRequest $request, int $id): UrgentNoticeResource
    {
        try {
            $urgentNotice = $this->repository->update($request->except('id'), $id);

            return new UrgentNoticeResource($urgentNotice);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }
}
