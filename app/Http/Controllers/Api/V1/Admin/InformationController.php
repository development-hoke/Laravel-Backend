<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Information\CreateRequest;
use App\Http\Requests\Api\V1\Admin\Information\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\Information\IndexRequest;
use App\Http\Requests\Api\V1\Admin\Information\ShowRequest;
use App\Http\Requests\Api\V1\Admin\Information\StorePreviewRequest;
use App\Http\Requests\Api\V1\Admin\Information\UpdateRequest;
use App\Http\Resources\Information as InformationResource;
use App\Repositories\InformationRepository;
use App\Services\Admin\InformationPreviewServiceInterface as InformationPreviewService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InformationController extends ApiAdminController
{
    /**
     * @var InformationRepository
     */
    private $repository;

    /**
     * @var InformationPreviewService
     */
    private $informationPreviewService;

    /**
     * @param InformationRepository $repository
     * @param InformationPreviewService $informationPreviewService
     */
    public function __construct(
        InformationRepository $repository,
        InformationPreviewService $informationPreviewService
    ) {
        $this->repository = $repository;
        $this->informationPreviewService = $informationPreviewService;
    }

    /**
     * @param IndexRequest $request
     *
     * @return InformationResource
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();
        $this->repository->pushCriteria(new \App\Criteria\Information\AdminSortCriteria($params));
        $information = $this->repository->paginate(config('repository.pagination.admin_limit'));

        return InformationResource::collection($information);
    }

    /**
     * @param ShowRequest $request
     * @param int $id
     *
     * @return InformationResource
     */
    public function show(ShowRequest $request, int $id): InformationResource
    {
        try {
            $information = $this->repository->find($id);

            return new InformationResource($information);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param CreateRequest $request
     *
     * @return InformationResource
     */
    public function store(CreateRequest $request): InformationResource
    {
        $information = $this->repository->create($request->except('id'));

        return new InformationResource($information);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return InformationResource
     */
    public function update(UpdateRequest $request, int $id): InformationResource
    {
        try {
            $information = $this->repository->update($request->except('id'), $id);

            return new InformationResource($information);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param DeleteRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRequest $request, int $id)
    {
        $this->repository->delete($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param StorePreviewRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function storePreview(StorePreviewRequest $request)
    {
        $cachedInfo = $this->informationPreviewService->store($request->validated());

        return new \Illuminate\Http\Resources\Json\JsonResource($cachedInfo);
    }

    /**
     * @param string $key
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function showPreview(string $key)
    {
        $preview = $this->informationPreviewService->fetch($key);

        return new \Illuminate\Http\Resources\Json\JsonResource($preview);
    }
}
