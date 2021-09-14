<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Requests\Api\V1\Front\Cart\CouponRequest;
use App\Http\Requests\Api\V1\Front\Cart\IndexRequest;
use App\Http\Requests\Api\V1\Front\Cart\RemoveItemRequest;
use App\Http\Requests\Api\V1\Front\Cart\RestoreItemRequest;
use App\Http\Requests\Api\V1\Front\Cart\StoreRequest;
use App\Http\Requests\Api\V1\Front\Cart\UpdateItemRequest;
use App\Http\Requests\Api\V1\Front\Cart\UpdateUsePointRequest;
use App\HttpCommunication\Ymdy\MemberInterface;
use App\Services\Front\CartServiceInterface;
use App\Services\Front\MemberServiceInterface;
use App\Services\Front\PointServiceInterface;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /** @var CartServiceInterface */
    protected $cartService;

    /** @var PointServiceInterface */
    protected $pointService;

    /** @var MemberInterface */
    protected $memberService;

    public function __construct(
        CartServiceInterface $cartService,
        PointServiceInterface $pointService,
        MemberServiceInterface $memberService
    ) {
        $this->cartService = $cartService;
        $this->pointService = $pointService;
        $this->memberService = $memberService;

        $this->middleware('auth:api')->only(['coupon']);
    }

    /**
     * カート情報取得
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();

        $cart = $this->cartService->findOrNew($params);

        $cart = $this->cartService->transitionReservationToNormalOrder($cart->id);

        // 会員利用可能クーポン一覧API
        $cart = $this->cartService->loadMemberCoupons($cart);
        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);
        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices);

        $cartData = $this->cartService->createCartData($cart, $pointData, $prices);

        if (isset($params['delete_expired']) && $params['delete_expired']) {
            $this->cartService->deleteExpiredCartItems($cart->id);
        }

        return response()->json($cartData);
    }

    /**
     * カートに入れる(通常購入・予約購入)
     * (カートに入れた数量変更)
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $params = $request->validated();
        // カートに商品追加
        $cart = $this->cartService->addItem($params);
        // 費用計算
        $prices = $this->cartService->calculatePrices($cart);
        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices);

        $cartData = $this->cartService->createCartData($cart, $pointData, $prices);

        return response()->json($cartData);
    }

    /**
     * カート商品更新
     *
     * @param UpdateItemRequest $request
     * @param int $cartId
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateItem(UpdateItemRequest $request, $cartId, $id)
    {
        $params = $request->validated();

        $cart = $this->cartService->updateItem($cartId, $id, $params);
        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);
        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices);

        $cartData = $this->cartService->createCartData($cart, $pointData, $prices);

        return response()->json($cartData);
    }

    /**
     * カート再投入
     *
     * @param RestoreItemRequest $request
     * @param int $cartId
     * @param int $itemDetailId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreItem(RestoreItemRequest $request, int $cartId, int $itemDetailId)
    {
        $params = $request->validated();

        $cart = $this->cartService->restoreItem($cartId, $itemDetailId, $params);
        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);
        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices);

        $cartData = $this->cartService->createCartData($cart, $pointData, $prices);

        return response()->json($cartData);
    }

    /**
     * カート商品削除
     *
     * @param RemoveItemRequest $request
     * @param int $cartId
     * @param int $itemDetailId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItem(RemoveItemRequest $request, int $cartId, int $itemDetailId)
    {
        $params = $request->validated();

        $cart = $this->cartService->removeItem($cartId, $itemDetailId, $params);
        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);
        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices);

        $cartData = $this->cartService->createCartData($cart, $pointData, $prices);

        return response()->json($cartData);
    }

    /**
     * クーポン適用
     *
     * @param CouponRequest $request
     *
     * @return array
     */
    public function coupon(CouponRequest $request)
    {
        $params = $request->validated();

        $cart = $this->cartService->updateUseCouponIds($params);
        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);
        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices);

        return $this->cartService->createCartData($cart, $pointData, $prices);
    }

    /**
     * 利用ポイント更新
     *
     * @param int $cartId
     * @param UpdateUsePointRequest $request
     *
     * @return array
     */
    public function updateUsePoint(int $cartId, UpdateUsePointRequest $request)
    {
        $params = $request->validated();

        $cart = $this->cartService->findAuthorizedCart($cartId);
        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);
        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices);

        return $this->cartService->createCartData($cart, $pointData, $prices);
    }
}
