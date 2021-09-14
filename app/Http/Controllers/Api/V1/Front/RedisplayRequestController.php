<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\RedisplayRequest\DestroyRequet;
use App\Http\Requests\Api\V1\Front\RedisplayRequest\IndexRequet;
use App\Http\Requests\Api\V1\Front\RedisplayRequest\ShowRequet;
use App\Http\Requests\Api\V1\Front\RedisplayRequest\StoreRequet;
use App\Http\Requests\Api\V1\Front\RedisplayRequest\ValidateEmailRequet;
use App\Http\Resources\ItemDetailRedisplayRequest as ItemDetailRedisplayRequestResource;
use App\Repositories\ItemDetailRedisplayRequestRepository;
use App\Repositories\ItemRepository;
use App\Services\Front\RedisplayRequestServiceInterface as RedisplayRequestService;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedisplayRequestController extends Controller
{
    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var ItemDetailRedisplayRequestRepository
     */
    private $redisplayRequestRepository;

    /**
     * @var RedisplayRequestService
     */
    private $redisplayRequestService;

    public function __construct(
        ItemRepository $itemRepository,
        ItemDetailRedisplayRequestRepository $redisplayRequestRepository,
        RedisplayRequestService $redisplayRequestService
    ) {
        $this->itemRepository = $itemRepository;
        $this->redisplayRequestRepository = $redisplayRequestRepository;
        $this->redisplayRequestService = $redisplayRequestService;
    }

    /**
     * @param IndexRequet $request
     * @param string $productNumber
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequet $request, string $productNumber)
    {
        $item = $this->itemRepository->findWhere(['product_number' => $productNumber])->first();

        if (empty($item)) {
            throw new NotFoundHttpException(error_format('error.model_not_found'));
        }

        $params = Arr::except($request->validated(), ['email_confirmation']);

        $params['item_id'] = $item->id;

        if (auth('api')->check()) {
            $params['member_id'] = auth('api')->id();
        }

        $redispalyRequests = $this->redisplayRequestRepository->findRedisplayRequests($params);

        // NOTE: GETでやるべきではない処理だが、リクエストを投げ直さなければいけないのでここでmember_idを更新する。
        if (!$redispalyRequests->isEmpty() && auth('api')->check()) {
            $this->redisplayRequestRepository->saveMemberId($redispalyRequests, $params['member_id']);
        }

        return ItemDetailRedisplayRequestResource::collection($redispalyRequests);
    }

    /**
     * @param ShowRequet $request
     * @param int $itemDetailId
     *
     * @return ItemDetailRedisplayRequestResource|\Illuminate\Http\Response
     */
    public function show(ShowRequet $request, int $itemDetailId)
    {
        $params = $request->validated();
        $conditions = [];

        if (auth('api')->check()) {
            $conditions['member_id'] = auth('api')->id();
        } elseif (!empty($params['user_token'])) {
            $conditions['user_token'] = $params['user_token'];
        }

        $conditions['item_detail_id'] = $itemDetailId;

        $redispalyRequest = $this->redisplayRequestRepository->findWhere($conditions)->first();

        if (empty($redispalyRequest)) {
            throw new NotFoundHttpException(error_format('error.model_not_found'));
        }

        return new ItemDetailRedisplayRequestResource($redispalyRequest);
    }

    /**
     * @param StoreRequet $request
     *
     * @return ItemDetailRedisplayRequestResource
     */
    public function store(StoreRequet $request)
    {
        $params = $request->validated();

        if (auth('api')->check()) {
            $params['member_id'] = auth('api')->id();
        }

        $redispalyRequest = $this->redisplayRequestService->acceptNewRequest($params);

        return new ItemDetailRedisplayRequestResource($redispalyRequest);
    }

    /**
     * @param DestroyRequet $request
     * @param int $itemDetailId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequet $request, int $itemDetailId)
    {
        $params = $request->validated();
        $conditions = [];

        if (auth('api')->check()) {
            $conditions['member_id'] = auth('api')->id();
        } elseif (!empty($params['user_token'])) {
            $conditions['user_token'] = $params['user_token'];
        }

        $conditions['item_detail_id'] = $itemDetailId;

        $this->redisplayRequestService->destroy($conditions);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * フォーム検証用
     *
     * @param ValidateEmailRequet $request
     *
     * @return \Illuminate\Http\Response
     */
    public function validateEmail(ValidateEmailRequet $request)
    {
        return response(null, Response::HTTP_OK);
    }
}
