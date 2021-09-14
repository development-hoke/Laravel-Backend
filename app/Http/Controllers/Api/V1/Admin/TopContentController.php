<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Criteria\TopContent\AdminIndexCriteria;
use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\TopContent\AddMainVisualsRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\AddNewItemsRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\AddPickupsRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\IndexRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\ShowByStoreBrandRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\UpdateBackgroundColorRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\UpdateFeatureRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\UpdateMainVisualRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\UpdateNewItemsRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\UpdateNewsRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\UpdatePickupsRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\UpdateSortFeaturesRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\UpdateSortNewsRequest;
use App\Http\Requests\Api\V1\Admin\TopContent\UpdateStatusMainVisualsRequest;
use App\Http\Resources\TopContent as TopContentResource;
use App\Repositories\TopContentAdminRepository;
use App\Services\Admin\TopContentServiceInterface;
use Illuminate\Http\Response;

class TopContentController extends ApiAdminController
{
    /**
     * @var TopContentAdminRepository
     */
    private $topContentRepository;

    /**
     * @var TopContentServiceInterface
     */
    private $topContentService;

    /**
     * @param TopContentAdminRepository $topContentRepository
     */
    public function __construct(
        TopContentAdminRepository $topContentRepository,
        TopContentServiceInterface $topContentService
    ) {
        $this->topContentRepository = $topContentRepository;
        $this->topContentService = $topContentService;
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $topContent = $this->topContentRepository->pushCriteria(new AdminIndexCriteria($request->validated()))->get();

        return TopContentResource::collection($topContent);
    }

    /**
     * @param IndexRequest $request
     *
     * @return TopContentResource
     */
    public function showByStoreBrand(ShowByStoreBrandRequest $request)
    {
        $storeBrand = $request->route('store_brand');

        $topContent = $this->topContentService->fetchOneByStoreBrand($storeBrand ?? null);

        return new TopContentResource($topContent);
    }

    /**
     * メインバナーの追加
     *
     * @param AddMainVisualsRequest $request
     * @param int $id
     *
     * @return TopContentResource
     */
    public function addMainVisuals(AddMainVisualsRequest $request, int $id)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->addMainVisual($params, $id);

        return new TopContentResource($topContent);
    }

    /**
     * メインバナーのソート更新
     *
     * @param UpdateMainVisualRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function updateMainVisuals(UpdateMainVisualRequest $request, int $id)
    {
        $params = $request->validated();
        // NOTE: 更新はソート変更のみなので画像の削除は行わないが、
        // 仕様変更があった場合に実装漏れがないように気をつける。
        $topContent = $this->topContentService->updateMainVisual($params, $id);

        return new TopContentResource($topContent);
    }

    /**
     * メインバナーのステータス更新
     *
     * @param UpdateStatusMainVisualsRequest $request
     * @param int $id
     * @param int $itemId
     *
     * @return TopContentResource
     */
    public function updateStatusMainVisuals(UpdateStatusMainVisualsRequest $request, int $id, int $sort)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->updateStatusMainVisual($id, $sort, $params);

        return new TopContentResource($topContent);
    }

    /**
     * メインバナーの削除
     *
     * @param int $id
     * @param int $sort
     */
    public function deleteMainVisuals(int $id, int $sort)
    {
        $this->topContentService->deleteMainVisual($id, $sort);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * 新着商品の追加
     *
     * @param AddNewItemsRequest $request
     * @param int $id
     *
     * @return TopContentResource
     */
    public function addNewItems(AddNewItemsRequest $request, int $id)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->addNewItems($params, $id);

        return new TopContentResource($topContent);
    }

    /**
     * 新着商品の更新
     *
     * @param UpdateNewItemsRequest $request
     * @param int $id
     * @param int $itemId
     *
     * @return TopContentResource
     */
    public function updateNewItems(UpdateNewItemsRequest $request, int $id, int $itemId)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->updateNewItem($id, $itemId, $params);

        return new TopContentResource($topContent);
    }

    /**
     * 新着商品の削除
     *
     * @param int $id
     * @param int $itemId
     *
     * @return TopContentResource
     */
    public function deleteNewItems(int $id, int $itemId)
    {
        $this->topContentService->deleteNewItem($id, $itemId);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * おすすめ商品の追加
     *
     * @param AddPickupsRequest $request
     * @param int $id
     *
     * @return TopContentResource
     */
    public function addPickups(AddPickupsRequest $request, int $id)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->addPickups($params, $id);

        return new TopContentResource($topContent);
    }

    /**
     * おすすめ商品の更新
     *
     * @param UpdatePickupsRequest $request
     * @param int $id
     * @param int $itemId
     *
     * @return TopContentResource
     */
    public function updatePickups(UpdatePickupsRequest $request, int $id, int $itemId)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->updatePickup($id, $itemId, $params);

        return new TopContentResource($topContent);
    }

    /**
     * おすすめ商品の削除
     *
     * @param int $id
     * @param int $itemId
     *
     * @return TopContentResource
     */
    public function deletePickups(int $id, int $itemId)
    {
        $this->topContentService->deletePickup($id, $itemId);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * 特集の背景色の更新
     *
     * @param int $id
     *
     * @return TopContentResource
     */
    public function updateBackgroundColor(UpdateBackgroundColorRequest $request, int $id)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->updateBackgroundColor($id, $params);

        return new TopContentResource($topContent);
    }

    /**
     * 特集の更新
     *
     * @param int $id
     *
     * @return TopContentResource
     */
    public function updateFeatures(UpdateFeatureRequest $request, int $id)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->updateFeatures($id, $params);

        return new TopContentResource($topContent);
    }

    /**
     * 特集のソート更新
     *
     * @param UpdateSortFeaturesRequest $request
     * @param int $id
     * @param int $planId
     *
     * @return TopContentResource
     */
    public function updateSortFeatures(UpdateSortFeaturesRequest $request, int $id, int $planId)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->updateSortFeatures($id, $planId, $params);

        return new TopContentResource($topContent);
    }

    /**
     * NEWSの更新
     *
     * @param int $id
     *
     * @return TopContentResource
     */
    public function updateNews(UpdateNewsRequest $request, int $id)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->updateNews($id, $params);

        return new TopContentResource($topContent);
    }

    /**
     * NEWSのソート更新
     *
     * @param UpdateSortNewsRequest $request
     * @param int $id
     * @param int $planId
     *
     * @return TopContentResource
     */
    public function updateSortNews(UpdateSortNewsRequest $request, int $id, int $planId)
    {
        $params = $request->validated();

        $topContent = $this->topContentService->updateSortNews($id, $planId, $params);

        return new TopContentResource($topContent);
    }
}
