<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Domain\StockInterface as StockService;
use App\Exceptions\InvalidInputException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Purchase\ChangePaymentTypeRequest;
use App\Http\Requests\Api\V1\Front\Purchase\ConfirmRequest;
use App\Http\Requests\Api\V1\Front\Purchase\DestroyMemberCreditCardRequest;
use App\Http\Requests\Api\V1\Front\Purchase\OrderRequest;
use App\Http\Response;
use App\Services\Front\CartServiceInterface;
use App\Services\Front\PointServiceInterface;
use App\Services\Front\PurchaseServiceInterface;
use App\Utils\OrderLog;
use Illuminate\Support\Facades\Session;

class PurchaseController extends Controller
{
    /** @var CartServiceInterface */
    protected $cartService;

    /** @var PurchaseServiceInterface */
    protected $purchaseService;

    /** @var PointServiceInterface */
    protected $pointService;

    /** @var StockService */
    protected $stockService;

    public function __construct(
        CartServiceInterface $cartService,
        PurchaseServiceInterface $purchaseService,
        PointServiceInterface $pointService,
        StockService $stockService
    ) {
        $this->cartService = $cartService;
        $this->purchaseService = $purchaseService;
        $this->pointService = $pointService;
        $this->stockService = $stockService;

        $this->middleware('auth:api')->only([
            'changePaymentType',
            'confirmAmazonPayOrder',
            'showMemberCreditCard',
            'destroyMemberCreditCard',
        ]);
    }

    /**
     * お支払い方法を変更
     * ログイン済みの会員のみに提供
     *
     * @param ChangePaymentTypeRequest $request
     */
    public function changePaymentType(ChangePaymentTypeRequest $request)
    {
        $params = $request->validated();

        $cart = $this->cartService->findAuthorizedCart($params['cart_id']);

        if (!$cart) {
            throw new InvalidInputException(__('error.no_cart'));
        }
        // 会員利用可能クーポン一覧API
        $cart = $this->cartService->loadMemberCoupons($cart);
        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);
        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices);

        return $this->cartService->createCartData($cart, $pointData, $prices);
    }

    /**
     * 注文確認
     *
     * @param ConfirmRequest $request
     *
     * @return array
     */
    public function confirm(ConfirmRequest $request)
    {
        $params = $request->validated();

        $cart = $this->cartService->findAuthorizedCart($params['cart_id'], $params['cart_token'] ?? null);

        if (!$this->cartService->validateReservation($cart)) {
            $cart = $this->cartService->transitionReservationToNormalOrder($cart->id, true);
        }

        // 在庫チェック
        if (!$this->stockService->hasStockForCart($cart, true)) {
            throw new InvalidInputException(__('error.no_ec_stock'));
        }

        // 会員利用可能クーポン一覧API
        if (!empty($cart->member_id)) {
            $cart = $this->cartService->loadValidatedMemberCoupons($cart);
        }

        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);

        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices, $params);

        if ($params['use_point'] > $pointData['effective_point']) {
            throw new InvalidInputException(__('error.over_use_point'));
        }

        $cartData = $this->cartService->createCartData($cart, $pointData, $prices);

        $responseData = array_merge($params, ['cart_data' => $cartData]);

        if (!$this->validateNoValidCartItem($responseData['cart_data'])) {
            return response()->json(array_merge($responseData, [
                'error_code' => \App\Enums\Common\ErrorCode::NoValidCartItem,
            ]), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($responseData, Response::HTTP_OK);
    }

    /**
     * AmazonPay注文確認
     * ログイン済みの会員のみに提供
     *
     * @param ConfirmRequest $request
     *
     * @return array
     */
    public function confirmAmazonPayOrder(ConfirmRequest $request)
    {
        $params = $request->validated();

        $cart = $this->cartService->findAuthorizedCart($params['cart_id']);

        if (!$this->cartService->validateReservation($cart)) {
            $cart = $this->cartService->transitionReservationToNormalOrder($cart->id, true);
        }

        if (!$this->stockService->hasStockForCart($cart, true)) {
            throw new InvalidInputException(__('error.no_ec_stock'));
        }

        if (!empty($cart->member_id)) {
            $cart = $this->cartService->loadValidatedMemberCoupons($cart);
        }

        $prices = $this->cartService->calculatePrices($cart, $params);

        $pointData = $this->pointService->getPoint($cart, $prices, $params);

        $orderReferenceDetails = $this->cartService->amazonPayOrderConfirm($params, $prices['total_price'], $params['amazon_access_token']);

        $params = $this->mergeAmazonDestinationWithParams($params, $orderReferenceDetails);

        if ($params['use_point'] > $pointData['effective_point']) {
            throw new InvalidInputException(__('error.over_use_point'));
        }

        $cartData = $this->cartService->createCartData($cart, $pointData, $prices);

        $responseData = array_merge($params, ['cart_data' => $cartData]);

        if (!$this->validateNoValidCartItem($responseData['cart_data'])) {
            return response()->json(array_merge($responseData, [
                'error_code' => \App\Enums\Common\ErrorCode::NoValidCartItem,
            ]), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($responseData, Response::HTTP_OK);
    }

    /**
     * @param array $params
     * @param \App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails
     *
     * @return array
     */
    private function mergeAmazonDestinationWithParams(array $params, \App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails)
    {
        $address = $orderReferenceDetails->destination->physical_destination;
        $params['amazon_destination'] = $address->toArray();

        return $params;
    }

    /**
     * 注文処理
     *
     * @param OrderRequest $request
     *
     * @return array
     *
     * @throws InvalidInputException
     */
    public function order(OrderRequest $request)
    {
        $params = $request->validated();
        OrderLog::unPurchased(Session::getId(), __FUNCTION__, $params);

        $cart = $this->cartService->findAuthorizedCart($params['cart_id'], $params['cart_token'] ?? null);

        if (!$this->cartService->validateReservation($cart)) {
            $cart = $this->cartService->transitionReservationToNormalOrder($cart->id, true);
        }

        // 在庫チェック
        if (!$this->stockService->hasStockForCart($cart, true)) {
            OrderLog::unPurchased(Session::getId(), __('error.no_cart'), $params);
            throw new InvalidInputException(__('error.no_ec_stock'), \App\Enums\Common\ErrorCode::StockShortage);
        }
        // 会員利用可能クーポン一覧API
        if (!empty($cart->member_id)) {
            $cart = $this->cartService->loadValidatedMemberCoupons($cart);
        }
        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);
        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices, $params);

        if ($params['use_point'] > $pointData['effective_point']) {
            throw new InvalidInputException(__('error.over_use_point'));
        }
        $cartData = $this->cartService->createCartData($cart, $pointData, $prices);

        if (!$this->validateNoValidCartItem($cartData)) {
            return response()->json(array_merge($params, [
                'cart_data' => $cartData,
                'error_code' => \App\Enums\Common\ErrorCode::NoValidCartItem,
            ]), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!empty($cartData['disabled_item_details'])) {
            throw new InvalidInputException(__('error.disabled_item_details_in_cart'));
        }

        // 注文処理
        $results = $this->purchaseService->order($cart, array_merge($params, $cartData, $pointData, [
            'is_guest' => false,
            'ip' => $request->ip(),
        ]));

        return array_merge($params, [
            'cart_data' => $cartData,
            'order' => [
                'code' => $results['order']->code,
            ],
            'new_cart' => [
                'id' => $results['new_cart']->id,
                'token' => $results['new_cart']->token,
            ],
        ]);
    }

    /**
     * 有効な商品が最低一つでもあるか検証
     *
     * @param array $cartData
     *
     * @return bool
     */
    private function validateNoValidCartItem(array $cartData)
    {
        $validItemCount = collect($cartData['items'])->filter(function ($item) {
            return !$item['lapsed'];
        })->count();

        return $validItemCount > 0;
    }

    /**
     * 保存済みカード情報取得
     *
     * @return \App\Http\Resources\MemberCreditCard|\Illuminate\Http\Response
     */
    public function showMemberCreditCard()
    {
        $memberCreditCard = $this->purchaseService->fetchCreditCardInfo();

        if (empty($memberCreditCard)) {
            return response(null, Response::HTTP_NO_CONTENT);
        }

        return new \App\Http\Resources\MemberCreditCard($memberCreditCard);
    }

    /**
     * 保存済みカード情報削除
     *
     * @param DestroyMemberCreditCardRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyMemberCreditCard(DestroyMemberCreditCardRequest $request, int $id)
    {
        $this->purchaseService->deleteCreditCardInfo($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
