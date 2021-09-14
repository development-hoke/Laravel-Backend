<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Domain\StockInterface as StockService;
use App\Exceptions\InvalidInputException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\GuestPurchase\ConfirmRequest;
use App\Http\Requests\Api\V1\Front\GuestPurchase\EmailAuthRequet;
use App\Http\Requests\Api\V1\Front\GuestPurchase\OrderRequest;
use App\Http\Requests\Api\V1\Front\GuestPurchase\VerifyRequet;
use App\Http\Response;
use App\Services\Front\CartServiceInterface;
use App\Services\Front\GuestPurchaseServiceInterface;
use App\Services\Front\PointServiceInterface;
use App\Services\Front\PurchaseServiceInterface;
use App\Utils\OrderLog;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Session;

class GuestPurchaseController extends Controller
{
    /**
     * @var GuestPurchaseServiceInterface
     */
    private $guestPurchaseService;

    /**
     * @var CartServiceInterface
     */
    protected $cartService;

    /**
     * @var PurchaseServiceInterface
     */
    protected $purchaseService;

    /**
     * @var PointServiceInterface
     */
    protected $pointService;

    /**
     * @var StockService
     */
    protected $stockService;

    /**
     * @param GuestPurchaseServiceInterface $guestPurchaseService
     * @param CartServiceInterface $cartService
     * @param PurchaseServiceInterface $purchaseService
     * @param PointServiceInterface $pointService
     * @param StockService $stockService
     */
    public function __construct(
        GuestPurchaseServiceInterface $guestPurchaseService,
        CartServiceInterface $cartService,
        PurchaseServiceInterface $purchaseService,
        PointServiceInterface $pointService,
        StockService $stockService
    ) {
        $this->guestPurchaseService = $guestPurchaseService;
        $this->cartService = $cartService;
        $this->purchaseService = $purchaseService;
        $this->pointService = $pointService;
        $this->stockService = $stockService;
    }

    /**
     * ゲスト購入メール認証
     *
     * @param EmailAuthRequet $request
     *
     * @return \Illuminate\Http\Response
     */
    public function emailAuth(int $cartId, EmailAuthRequet $request)
    {
        $params = $request->validated();

        $data = $this->guestPurchaseService->emailAuth($cartId, $params);

        return new JsonResource([
            'email' => $data['member']['email'],
        ]);
    }

    /**
     * ゲスト購入メール認証チェック
     *
     * @param VerifyRequet $request
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(int $cartId, VerifyRequet $request)
    {
        $params = $request->validated();

        $this->guestPurchaseService->verify($cartId, $params);

        return response(null, Response::HTTP_OK);
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

        $cart = $this->cartService->findCartByToken($params['cart_id'], $params['cart_token']);

        // 在庫チェック
        if (!$this->stockService->hasStockForCart($cart, true)) {
            throw new InvalidInputException(__('error.no_ec_stock'));
        }

        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);

        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices, $params);

        $cartData = $this->cartService->createCartData($cart, $pointData, $prices);

        $memberData = $this->guestPurchaseService->fetchMemberDetail($cart->member_id, $params['member_token']);

        $responseData = array_merge($params, [
            'cart_data' => $cartData,
            'member' => [
                'email' => $memberData['email'],
            ],
        ]);

        if (!$this->validateNoValidCartItem($responseData['cart_data'])) {
            return response()->json(array_merge($responseData, [
                'error_code' => \App\Enums\Common\ErrorCode::NoValidCartItem,
            ]), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($responseData, Response::HTTP_OK);
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

        $cart = $this->cartService->findCartByToken($params['cart_id'], $params['cart_token']);

        // 在庫チェック
        if (!$this->stockService->hasStockForCart($cart, true)) {
            OrderLog::unPurchased(Session::getId(), __('error.no_cart'), $params);
            throw new InvalidInputException(__('error.no_ec_stock'), \App\Enums\Common\ErrorCode::StockShortage);
        }

        // 費用計算
        $prices = $this->cartService->calculatePrices($cart, $params);

        // 購買時ポイント計算API
        $pointData = $this->pointService->getPoint($cart, $prices, $params);

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
        $params['card']['use_saved_card_info'] = false;
        $params['card']['is_save_card_info'] = false;

        $memberActivateLink = \App\Utils\Url::resolveFrontUrl('register_members_activate', [], [
            'member_id' => $cart->member_id,
            'member_token' => $params['member_token'],
            'is_guest' => 1,
        ]);

        $results = $this->purchaseService->order($cart, array_merge($params, $cartData, $pointData, [
            'use_point' => 0,
            'is_guest' => true,
            'ip' => $request->ip(),
            'member_activate_link' => $memberActivateLink,
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
            'member_activate_link' => $memberActivateLink,
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
}
