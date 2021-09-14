<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Exceptions\InvalidInputException;
use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Plan\AddNewItemsRequest;
use App\Http\Requests\Api\V1\Admin\Plan\CopyRequest;
use App\Http\Requests\Api\V1\Admin\Plan\CreateRequest;
use App\Http\Requests\Api\V1\Admin\Plan\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\Plan\IndexRequest;
use App\Http\Requests\Api\V1\Admin\Plan\ShowByStoreBrandRequest;
use App\Http\Requests\Api\V1\Admin\Plan\ShowRequest;
use App\Http\Requests\Api\V1\Admin\Plan\UpdateItemSettingRequest;
use App\Http\Requests\Api\V1\Admin\Plan\UpdateRequest;
use App\Http\Resources\Plan as PlanResource;
use App\Repositories\PlanRepository;
use App\Repositories\TopContentRepository;
use App\Services\Admin\PlanServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PlanController extends ApiAdminController
{
    /**
     * @var PlanRepository
     */
    private $repository;

    /**
     * @var PlanServiceInterface
     */
    private $service;

    /**
     * @var TopContentRepository
     */
    private $topContentRepository;

    /**
     * @param PlanRepository $repository
     * @param PlanServiceInterface $service
     * @param TopContentRepository $topContentRepository
     */
    public function __construct(PlanRepository $repository, PlanServiceInterface $service, TopContentRepository $topContentRepository)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->topContentRepository = $topContentRepository;
    }

    /**
     * @param IndexRequest $request
     *
     * @return PlanResource
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();
        $this->repository->pushCriteria(new \App\Criteria\Plan\AdminPlanCriteria($params));
        $plan = $this->repository->paginate(config('repository.pagination.admin_limit'));

        return PlanResource::collection($plan);
    }

    /**
     * @param ShowRequest $request
     * @param int $id
     *
     * @return PlanResource
     */
    public function show(ShowRequest $request, int $id): PlanResource
    {
        try {
            $plan = $this->repository->with([
                'items.itemDetails',
            ])->find($id);

            return new PlanResource($plan);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param CreateRequest $request
     *
     * @return PlanResource
     */
    public function store(CreateRequest $request): PlanResource
    {
        $plan = $this->service->create($request->except('id'));

        $plan->load('items.itemDetails');

        return new PlanResource($plan);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return PlanResource
     */
    public function update(UpdateRequest $request, int $id): PlanResource
    {
        $params = $request->validated();

        $plan = $this->service->update($params, $id);

        $plan->load('items.itemDetails');

        return new PlanResource($plan);
    }

    /**
     * @param DeleteRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRequest $request, int $id)
    {
        $isPlanUsedInFeatures = $this->topContentRepository->isPlanUsedInFeatures($id);
        $isPlanUsedInNews = $this->topContentRepository->isPlanUsedInNews($id);

        if ($isPlanUsedInFeatures || $isPlanUsedInNews) {
            throw new InvalidInputException(error_format('error.plan_used_in_top_contents'), Response::HTTP_FORBIDDEN);
        }

        $this->service->delete($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param CopyRequest $request
     * @param int $id
     *
     * @return PageResource
     */
    public function copy(CopyRequest $request, int $id)
    {
        $plan = $this->service->copy($id);

        return new PlanResource($plan);
    }

    /**
     * 一覧商品の削除
     *
     * @param int $id
     * @param int $itemId
     *
     * @return PlanResource
     */
    public function deleteItems(int $id, int $itemId)
    {
        $this->service->deleteItem($id, $itemId);

        $plan = $this->repository->with([
            'items.itemDetails',
        ])->find($id);

        return new PlanResource($plan);
        // return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param UpdateItemSettingRequest $request
     * @param int $id
     *
     * @return PlanResource
     */
    public function updateItemSetting(UpdateItemSettingRequest $request, int $id): PlanResource
    {
        $params = $request->validated();

        $plan = $this->service->updateItemSetting($params, $id);

        $plan->load('items.itemDetails');

        return new PlanResource($plan);
    }

    /**
     * @param AddNewItemsRequest $request
     * @param int $id
     *
     * @return PlanResource
     */
    public function addNewItems(AddNewItemsRequest $request, int $id)
    {
        $params = $request->validated();

        $plan = $this->service->addNewItems($params, $id);

        $plan->load('items.itemDetails');

        return new PlanResource($plan);
    }

    /**
     * @param ShowByStoreBrandRequest $request
     *
     * @return PlanResource
     */
    public function showByStoreBrand(ShowByStoreBrandRequest $request)
    {
        $storeBrand = $request->route('store_brand');

        $plan = $this->service->fetchByStoreBrand($storeBrand ?? null);

        return PlanResource::collection($plan);
    }
}
