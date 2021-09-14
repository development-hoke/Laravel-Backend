<?php

namespace App\Services\Front;

use App\Domain\AmazonPayInterface as AmazonPayService;
use App\Domain\CouponInterface as DomainCouponService;
use App\Domain\ItemInterface as DomainItemService;
use App\Domain\StockInterface as StockService;
use App\Domain\Utils\OrderPrice;
use App\Enums\Cart\Status;
use App\Exceptions\FatalException;
use App\Exceptions\InvalidInputException;
use App\Models\Cart;
use App\Repositories\CartItemRepository;
use App\Repositories\CartRepository;
use App\Repositories\ClosedMarketRepository;
use App\Repositories\DeliverySettingRepository;
use App\Repositories\EventRepository;
use App\Repositories\ItemDetailRepository;
use App\Repositories\ItemRepository;
use App\Services\Service;
use App\Utils\Arr;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CartService extends Service implements CartServiceInterface
{
    /**
     * 数量初期値
     */
    private const DEFAULT_STOCK = 1;

    /** @var \App\Domain\ItemPrice */
    protected $itemPrice;

    /** @var CartRepository */
    protected $cartRepository;

    /** @var CartItemRepository */
    protected $cartItemRepository;

    /** @var DeliverySettingRepository */
    protected $deliverySettingRepository;

    /** @var ItemRepository */
    protected $itemRepository;

    /** @var ItemDetailRepository */
    protected $itemDetailRepository;

    /** @var EventRepository */
    protected $eventRepository;

    /** @var MemberService */
    protected $memberService;

    /** @var ItemDetailServiceInterface */
    protected $itemDetailService;

    /** @var DomainItemService */
    protected $domainItemService;

    /** @var DomainCouponService */
    protected $domainCouponService;

    /** @var ClosedMarketRepository */
    protected $closedMarketRepository;

    /** @var AmazonPayService */
    protected $amazonPayService;

    /** @var StockService */
    protected $stockService;

    public function __construct(
        \App\Domain\ItemPrice $itemPrice,
        CartRepository $cartRepository,
        CartItemRepository $cartItemRepository,
        DeliverySettingRepository $deliverySettingRepository,
        ItemRepository $itemRepository,
        ItemDetailRepository $itemDetailRepository,
        EventRepository $eventRepository,
        MemberService $memberService,
        ItemDetailServiceInterface $itemDetailService,
        DomainItemService $domainItemService,
        DomainCouponService $domainCouponService,
        ClosedMarketRepository $closedMarketRepository,
        AmazonPayService $amazonPayService,
        StockService $stockService
    ) {
        $this->itemPrice = $itemPrice;
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->deliverySettingRepository = $deliverySettingRepository;
        $this->itemRepository = $itemRepository;
        $this->itemDetailRepository = $itemDetailRepository;
        $this->eventRepository = $eventRepository;
        $this->memberService = $memberService;
        $this->itemDetailService = $itemDetailService;
        $this->domainItemService = $domainItemService;
        $this->domainCouponService = $domainCouponService;
        $this->closedMarketRepository = $closedMarketRepository;
        $this->amazonPayService = $amazonPayService;
        $this->stockService = $stockService;

        if (auth('api')->check()) {
            $user = auth('api')->user();
            $this->domainCouponService->setMemberToken($user->token);
        }
    }

    /**
     * 権限検証済みカートデータ取得
     *
     * @param int $cartId
     * @param string|null $token
     *
     * @return \App\Models\Cart
     */
    public function findAuthorizedCart(int $cartId, ?string $token = null)
    {
        $conditions = ['id' => $cartId];

        if (auth('api')->check()) {
            $conditions['member_id'] = auth('api')->id();
        } else {
            if (empty($token)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }

            $conditions['token'] = $token;
        }

        $cart = $this->cartRepository->findWhere($conditions)->first();

        if (empty($cart)) {
            throw new HttpException(Response::HTTP_NOT_FOUND);
        }

        return $cart;
    }

    /**
     * 権限検証済みカートデータ取得
     *
     * @param int $cartId
     * @param string|null $token
     *
     * @return \App\Models\Cart
     */
    public function findCartByToken(int $cartId, string $token)
    {
        $cart = $this->cartRepository->findWhere([
            'id' => $cartId,
            'token' => $token,
        ])->first();

        if (empty($cart)) {
            throw new HttpException(Response::HTTP_NOT_FOUND);
        }

        return $cart;
    }

    /**
     * カート取得
     * カートが存在しなければ新規作成
     *
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function findOrNew(array $params)
    {
        $token = $params['token'] ?? null;
        $cartId = $params['cart_id'] ?? null;
        $cart = null;

        if (auth('api')->check()) {
            $cart = $this->findAndMergeMemberCart($cartId, $token);
        }

        if (empty($cart) && !empty($cartId) && !empty($token)) {
            $cart = $this->findAndMergeTokenCart($cartId, $token);
        }

        if (empty($cart)) {
            $cart = $this->cartRepository->create([
                'member_id' => auth('api')->id(),
            ]);
        }

        return $cart;
    }

    /**
     * @param int|null $cartId
     * @param string|null $token
     *
     * @return \App\Models\Cart|null
     */
    private function findAndMergeMemberCart(?int $cartId = null, ?string $token = null)
    {
        $memberId = auth('api')->id();

        $cart = $this->cartRepository->findWhere(['member_id' => $memberId])->first();

        if (!$cart) {
            return;
        }

        if (empty($token) || empty($cartId)) {
            return $cart;
        }

        $oldCart = $this->cartRepository->with('cartItems')
            ->findWhere(['id' => $cartId, 'token' => $token])
            ->first();

        // 会員IDに紐づくカートとトークンに紐づくカートが競合する場合の処理
        if (!empty($oldCart) && (int) $oldCart->id !== (int) $cart->id) {
            if ($oldCart->cartItems->isNotEmpty()) {
                // 会員IDに紐づくカートとトークンに紐づくカートが競合する場合、
                // トークンに紐づくカートに投入された商品が直近に投入された商品になる。
                // 注文種別が異なっている場合、または1点以上の購入が出来ない注文種別の場合、トークンに紐づくカートの商品を使用する。
                if ($oldCart->order_type !== $cart->order_type || in_array($oldCart->order_type, [
                    \App\Enums\Order\OrderType::BackOrder,
                    \App\Enums\Order\OrderType::Reserve,
                ])) {
                    $this->cartItemRepository->deleteWhere(['cart_id' => $cart->id]);
                    $this->cartRepository->update(['order_type' => $oldCart->order_type], $cart->id);
                }

                foreach ($oldCart->cartItems as $cartItem) {
                    $this->cartItemRepository->update(['cart_id' => $cart->id], $cartItem->id);
                }
            }

            $this->cartRepository->delete($oldCart->id);
        }

        return $cart;
    }

    /**
     * @param int $cartId
     * @param string $token
     *
     * @return \App\Models\Cart|null
     */
    private function findAndMergeTokenCart(int $cartId, string $token)
    {
        $cart = $this->cartRepository->findWhere(['id' => $cartId, 'token' => $token])->first();

        if (empty($cart)) {
            return;
        }

        if (!auth('api')->check() && !$cart->is_guest && !empty($cart->member_id)) {
            return;
        }

        if (auth('api')->check()) {
            if (!empty($cart->member_id) && auth('api')->id() !== $cart->member_id) {
                return;
            }

            if (empty($cart->member_id)) {
                $cart = $this->cartRepository->update(['member_id' => auth('api')->id()], $cart->id);
            }
        }

        return $cart;
    }

    /**
     * カート商品生成
     *
     * @param array $params
     *
     * @return array
     */
    private function createCartItemAttributes(array $params)
    {
        $count = $params['count'] ?? self::DEFAULT_STOCK;

        $itemDetail = $this->itemDetailService->findBySKU(
            $params['product_number'],
            $params['color_id'],
            $params['size_id']
        );

        $itemData = [
            'item_detail_id' => $itemDetail->id,
            'count' => $count,
            'posted_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        if (!empty($params['closed_market_id'])) {
            $itemData['closed_market_id'] = $params['closed_market_id'];
            $itemData['is_closed_market'] = true;
        } else {
            $itemData['is_closed_market'] = false;
        }

        return $itemData;
    }

    /**
     * カートに追加(= 90分間在庫を抑える)
     *
     * @param array $params
     *
     * @return \App\Models\Cart
     *
     * @throws InvalidInputException
     */
    public function addItem(array $params)
    {
        try {
            DB::beginTransaction();

            $cart = $this->findOrNew($params);

            $cart->load(['cartItems']);

            if ($cart->cartItems->isNotEmpty()) {
                if ((int) $cart->order_type !== (int) $params['status']) {
                    throw new InvalidInputException(__('validation.cart.different_order_type', [
                        'type' => \App\Enums\Order\OrderType::getDescription($cart->order_type),
                    ]));
                }
                if ((int) $cart->order_type !== \App\Enums\Order\OrderType::Normal) {
                    throw new InvalidInputException(__('validation.cart.max_if', [
                        'type' => \App\Enums\Order\OrderType::getDescription($cart->order_type),
                        'max' => 1,
                    ]));
                }
            }

            if ($cart->cartItems->isEmpty()) {
                $cart = $this->cartRepository->update(['order_type' => null], $cart->id);
            }

            $attributes = $this->createCartItemAttributes($params);

            // 商品在庫確認
            if (!$this->stockService->lockAndValidateCartItemCount(new \App\Models\CartItem($attributes), $params['status'])) {
                throw new InvalidInputException(__('error.no_ec_stock'));
            }

            $cartItems = $this->cartItemRepository->findWhere(['cart_id' => $cart->id]);

            if ($cartItems->isEmpty()) {
                $cart->order_type = $params['status'];
            }

            // カート商品追加。投入時間を管理する必要があるので、常に新規作成する。
            $targetCartItem = $cartItems->where('item_detail_id', $attributes['item_detail_id'])->first();

            if (empty($targetCartItem)) {
                $this->cartItemRepository->create(array_merge($attributes, ['cart_id' => $cart->id]));
            } else {
                $this->cartItemRepository->update($attributes, $targetCartItem->id);
            }

            $cart = $this->cartRepository->update($cart->toArray(), $cart->id);

            // 商品の内容が変更になるため、再度クーポンの検証をする
            if (!empty($cart->use_coupon_ids)) {
                $cart->memberCoupons = $this->fetchValidatedMemberCoupons($cart->id, $cart->use_coupon_ids);
            }

            DB::commit();

            return $cart;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 期限切れのカート商品を削除する
     *
     * @param int $cartId
     *
     * @return void
     */
    public function deleteExpiredCartItems(int $cartId)
    {
        try {
            DB::beginTransaction();

            $cart = $this->cartRepository->with('cartItems')->find($cartId);

            $cart->getLapsedCartItems()->each(function ($cartItem) {
                $cartItem->delete();
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * 予約注文で商品が予約商品ではなくなった場合、通常注文の商品に移行する
     * 在庫確保はせずに、無効となった商品としてカートで表示する。
     *
     * @param int $cartId
     * @param bool|null $skipValidation
     *
     * @return \App\Models\Cart
     */
    public function transitionReservationToNormalOrder(int $cartId, ?bool $skipValidation = false)
    {
        $cart = $this->cartRepository->find($cartId);

        if (!$skipValidation && $this->validateReservation($cart)) {
            return $cart;
        }

        $cartItem = $cart->cartItems->first();

        try {
            DB::beginTransaction();

            $this->cartItemRepository->update([
                'invalid' => true,
                'invalid_reason' => \App\Enums\CartItem\InvalidReason::LapsedReservation,
            ], $cartItem->id);

            $cart = $this->cartRepository->update([
                'order_type' => \App\Enums\Order\OrderType::Normal,
            ], $cart->id);

            DB::commit();

            return $cart;
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * 予約商品の検証
     *
     * @param \App\Models\Cart $cart
     *
     * @return bool
     */
    public function validateReservation(\App\Models\Cart $cart)
    {
        if ($cart->order_type !== \App\Enums\Order\OrderType::Reserve) {
            return true;
        }

        $cartItem = $cart->getSecuredCartItems()->first();

        if (empty($cartItem)) {
            return true;
        }

        if ($cartItem->itemDetail->item->is_reservation) {
            return true;
        }

        return false;
    }

    /**
     * カート商品更新
     *
     * @param int $cartId
     * @param int $cartItemId
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function updateItem(int $cartId, int $cartItemId, array $params)
    {
        try {
            DB::beginTransaction();

            $cart = $this->findAuthorizedCart($cartId, $params['token'] ?? null);

            $cartItem = $this->cartItemRepository->findWhere(['cart_id' => $cart->id, 'id' => $cartItemId])->first();

            if (empty($cartItem)) {
                throw new HttpException(Response::HTTP_NOT_FOUND);
            }

            $addingCount = $params['count'] - $cartItem->count;

            $cartItem->fill(array_merge(
                Arr::except($params, ['token']),
                ['posted_at' => Carbon::now()->format('Y-m-d H:i:s')]
            ));

            if ($addingCount > 0) {
                $addingCartItem = $cartItem->replicate();
                $addingCartItem->count = $addingCount;

                if (!$this->stockService->lockAndValidateCartItemCount($addingCartItem, $cart->order_type)) {
                    throw new InvalidInputException(__('error.no_ec_stock'));
                }
            }

            $cartItem->save();

            $cart = $this->cartRepository->find($cart->id);

            // 商品の内容が変更になるため、再度クーポンの検証をする
            if (!empty($cart->use_coupon_ids)) {
                $cart->memberCoupons = $this->fetchValidatedMemberCoupons($cart->id, $cart->use_coupon_ids);
            }

            DB::commit();

            return $cart;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * カート再投入
     *
     * @param int $cartId
     * @param int $cartItemId
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function restoreItem(int $cartId, int $cartItemId, array $params)
    {
        try {
            DB::beginTransaction();

            $cart = $this->findAuthorizedCart($cartId, $params['token'] ?? null);

            $cartItem = $this->cartItemRepository->scopeQuery(function ($query) {
                return $query->withTrashed();
            })->findWhere(['cart_id' => $cart->id, 'id' => $cartItemId])->first();

            if (empty($cartItem)) {
                throw new HttpException(Response::HTTP_NOT_FOUND);
            }

            if (!$this->stockService->lockAndValidateCartItemCount($cartItem, $cart->order_type)) {
                if ((int) $cart->order_type === \App\Enums\Order\OrderType::Normal) {
                    if ($this->stockService->lockAndValidateCartItemCount($cartItem, \App\Enums\Order\OrderType::BackOrder)) {
                        throw new InvalidInputException(__('error.no_ec_stock'), \App\Enums\Common\ErrorCode::EcStockShortageButAvailableBackOrder);
                    }
                }

                throw new InvalidInputException(__('error.no_ec_stock'), \App\Enums\Common\ErrorCode::StockShortage);
            }

            $this->cartItemRepository->restore($cartItem->id);

            $cart = $this->cartRepository->find($cart->id);

            // 商品の内容が変更になるため、再度クーポンの検証をする
            if (!empty($cart->use_coupon_ids)) {
                $cart->memberCoupons = $this->fetchValidatedMemberCoupons($cart->id, $cart->use_coupon_ids);
            }

            DB::commit();

            return $cart;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * カート商品削除処理
     *
     * @param int $cartId
     * @param int $cartItemId
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function removeItem(int $cartId, int $cartItemId, array $params)
    {
        try {
            DB::beginTransaction();

            $cart = $this->findAuthorizedCart($cartId, $params['token'] ?? null);

            $cartItem = $this->cartItemRepository->findWhere(['cart_id' => $cart->id, 'id' => $cartItemId])->first();

            if (!empty($cartItem)) {
                $this->cartItemRepository->delete($cartItem->id);
            }

            $cart = $this->cartRepository->find($cart->id);

            // 商品の内容が変更になるため、再度クーポンの検証をする
            if (!empty($cart->use_coupon_ids)) {
                $cart->memberCoupons = $this->fetchValidatedMemberCoupons($cart->id, $cart->use_coupon_ids);
            }

            DB::commit();

            return $cart;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 利用できなくなった商品詳細を取得
     *
     * @param \App\Models\Cart $cart
     *
     * @return array
     */
    public function getDisabledItemDetails(\App\Models\Cart $cart)
    {
        $disabled = [];

        $now = Carbon::now();

        foreach ($cart->cartItems as $cartItem) {
            $itemDetail = $cartItem->itemDetail;

            if ((int) $itemDetail->status !== \App\Enums\Common\Status::Published) {
                $disabled[$itemDetail->id] = $itemDetail;
                continue;
            }

            $item = $itemDetail->item;

            if ($item->status !== \App\Enums\Common\Status::Published) {
                $disabled[$itemDetail->id] = $itemDetail;
                continue;
            }

            if (Carbon::parse($item->sales_period_from)->gt($now)) {
                $disabled[$itemDetail->id] = $itemDetail;
                continue;
            }

            if (Carbon::parse($item->sales_period_to)->lt($now)) {
                $disabled[$itemDetail->id] = $itemDetail;
                continue;
            }
        }

        return $disabled;
    }

    /**
     * カート商品一覧返却
     *
     * @param Cart $cart
     * @param array|null $member
     *
     * @return array|array[]
     */
    private function getCartItems(Cart $cart)
    {
        return $cart->cartItems->map(function ($cartItem) use ($cart) {
            $itemDetail = $cartItem->itemDetail;
            $item = $itemDetail->item;
            $brand = $itemDetail->item->brand;

            if (!$brand) {
                throw new FatalException(__('error.cart_invalid_brand'));
            }

            return [
                'id' => $item->id,
                'item_detail_id' => $itemDetail->id,
                'cart_item_id' => $cartItem->id,
                'status' => [
                    'value' => $cart->order_type,
                    'label' => Status::getDescription($cart->order_type),
                ],
                'brand' => [
                    'id' => $brand->id,
                    'name' => $brand->name,
                ],
                'name' => $item->name,
                'product_number' => $item->product_number,
                'maker_product_number' => $item->maker_product_number,
                'color' => [
                    'id' => $itemDetail->color_id,
                    'display_name' => $itemDetail->color->display_name,
                ],
                'size' => [
                    'id' => $itemDetail->size_id,
                    'name' => $itemDetail->size->name,
                ],
                'closed_market_id' => $cartItem['closed_market_id'] ?? null,
                'retail_price' => $item->retail_price,
                'displayed_sale_price' => $item->displayed_sale_price,
                'bundle_sale_price' => $item->bundle_sale_price,
                'price_before_order' => $item->price_before_order,
                'can_display_original_price' => $item->can_display_original_price,
                'cart_original_price' => $item->cart_original_price,
                'image_url' => $itemDetail->image_url,
                'count' => $cartItem['count'],
                'valid_time' => \App\Domain\Utils\Cart::calculateValidTime($cartItem->posted_at),
                'is_reservation' => $item->is_reservation,
                'applied_reservation' => $item->appliedReservation,
                'expired' => $cartItem->expired,
                'invalid' => $cartItem->invalid,
                'lapsed' => $cartItem->lapsed,
                'invalid_reason' => $cartItem->invalid_reason,
                'posted_at' => $cartItem->posted_at,
            ];
        })->toArray();
    }

    /**
     * @param Cart $cart
     * @param array $pointData
     * @param array $prices
     *
     * @return array
     *
     * @throws InternalException
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function createCartData(Cart $cart, array $pointData, array $prices)
    {
        if ($cart->member_id && !$cart->is_guest) {
            $memberResponse = $this->memberService->get($cart->member_id);
            $cart->member = $memberResponse['member'] ?? null;
        }

        $cart->load('cartItems.itemDetail.item');
        $cart = $this->itemPrice->fillPriceBeforeOrderToCreateNewOrder($cart);

        $memberCoupons = $this->domainCouponService->fetchAvailableCartMemberCoupons($cart);

        $disabledItemDetails = $this->getDisabledItemDetails($cart);

        return array_merge([
            'id' => $cart->id,
            'member_id' => $cart->member_id,
            'token' => $cart->token,
            'items' => $this->getCartItems($cart),
            'available_coupons' => $memberCoupons->pluck('coupon')->toVisibleArray(),
            'use_coupons' => \App\Entities\Ymdy\Member\MemberCoupon::collection(
                $cart->memberCoupons->toArray()
            )->pluck('coupon')->toVisibleArray(),
            'order_type' => $cart->order_type,
            'point' => $pointData['base_grant_point'] + $pointData['special_grant_point'],
            'disabled_item_details' => $disabledItemDetails,
        ], $prices);
    }

    /**
     * 検証済みメンバークーポンを取得する
     *
     * @param int $cartId
     * @param array $useCouponIds
     *
     * @return \App\Entities\Collection
     *
     * @throws InvalidInputException
     */
    public function fetchValidatedMemberCoupons(int $cartId, array $useCouponIds)
    {
        $cart = $this->cartRepository->find($cartId);

        if (empty($cart->member_id)) {
            throw new InvalidInputException(__('validation.coupon.invalid_request'));
        }

        $cart->load('cartItems.itemDetail.item');
        $cart = $this->itemPrice->fillPriceBeforeOrderToCreateNewOrder($cart);

        $validatedCoupons = $this->domainCouponService->fetchValidatedMemberCoupons(
            $cart->member_id,
            $useCouponIds,
            $cart
        );

        return $validatedCoupons;
    }

    /**
     * 費用計算
     *
     * @param \App\Models\Cart $cart
     * @param array|null $options
     *
     * @return array
     */
    public function calculatePrices(\App\Models\Cart $cart, ?array $options = [])
    {
        $paymentType = $options['payment_type'] ?? null;
        $usePoint = $options['use_point'] ?? null;

        if ($cart->member_id && !$cart->is_guest) {
            $memberResponse = $this->memberService->get($cart->member_id);
            $cart->member = $memberResponse['member'] ?? null;
        }

        $cart->load('cartItems.itemDetail.item');
        $cart = $this->itemPrice->fillPriceBeforeOrderToCreateNewOrder($cart);

        $cart = $this->domainCouponService->calculateCartCouponDiscount($cart);
        $couponDiscount = $cart->memberCoupons->pluck('coupon')->sum('discount_price');

        $originalPostage = $this->deliverySettingRepository->calculateDeliveryFee($cart->total_item_price_before_order);
        $this->calculateDiscountedDeliveryFee($cart);

        $campaignDiscount = $this->calculateCampaignDiscountTotalPrice($cart);
        $employeeDiscount = $this->calculateEmployeeDiscountTotalPrice($cart);
        $itemTotalPrice = $this->calculateItemTotalPriceInOrderContent($cart);

        // 手数料
        $paymentFee = isset($paymentType) ? OrderPrice::getPaymentFee($paymentType) : 0;

        $totalPrice = $cart->total_item_price_before_order + $cart->discounted_delivery_fee + $paymentFee - $couponDiscount - $usePoint;

        return [
            'items_total' => $itemTotalPrice,
            'campaign_discount' => $campaignDiscount,
            'coupon_discount' => $couponDiscount,
            'employee_discount' => $employeeDiscount,
            'postage' => $cart->delivery_fee,
            'discounted_postage' => $cart->discounted_delivery_fee,
            'original_postage' => $originalPostage,
            'total_price' => $totalPrice,
            'payment_fee' => $paymentFee,
        ];
    }

    /**
     * クーポン適用
     *
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function updateUseCouponIds(array $params)
    {
        $cart = $this->findAuthorizedCart($params['cart_id']);

        if (empty($cart)) {
            throw new HttpException(Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $useCouponIds = $params['use_coupon_ids'];

            $memberCoupons = $this->fetchValidatedMemberCoupons($cart->id, $useCouponIds);

            $cart = $this->cartRepository->update(['use_coupon_ids' => $useCouponIds], $cart->id);

            $cart->memberCoupons = $memberCoupons;

            DB::commit();

            return $cart;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * memberCouponの読み込み（バリデーションなし）
     *
     * @param \App\Models\Cart $cart
     *
     * @return \App\Models\Cart
     */
    public function loadMemberCoupons(Cart $cart)
    {
        if (empty($cart->member_id) || $cart->is_guest) {
            return $cart;
        }

        $memberCoupons = $this->domainCouponService->fetchAvailableMemberCoupons($cart->member_id);
        $useCouponIds = Arr::dict($cart->use_coupon_ids);
        $cart->memberCoupons = $memberCoupons->filter(function ($memberCoupon) use ($useCouponIds) {
            return isset($useCouponIds[$memberCoupon->coupon->id]);
        });

        return $cart;
    }

    /**
     * バリデーション済みのmemberCouponの読み込み
     *
     * @param \App\Models\Cart $cart
     *
     * @return \App\Models\Cart
     */
    public function loadValidatedMemberCoupons(\App\Models\Cart $cart)
    {
        if (empty($cart->use_coupon_ids)) {
            return $cart;
        }

        $cart->memberCoupons = $this->fetchValidatedMemberCoupons($cart->id, $cart->use_coupon_ids);

        return $cart;
    }

    /**
     * 割引適用後の送料計算
     * NOTE: fillPriceBeforeOrderToCreateNewOrder, calculateCartCouponDiscountが実行されているcartを引数に渡す
     *
     * @param Cart $cart
     *
     * @return int
     */
    private function calculateDiscountedDeliveryFee(Cart $cart)
    {
        $cart->delivery_fee = $this->deliverySettingRepository->getDefaultDeliveryFee();

        if ($cart->has_free_shipping_coupon) {
            $cart->discounted_delivery_fee = 0;
            $cart->delivery_fee_discount_type = \App\Enums\OrderDiscount\Type::CouponDeliveryFee;

            return $cart;
        }

        if ((int) $cart->order_type === \App\Enums\Order\OrderType::Reserve) {
            if ($this->hasFreeDeliveryReservation($cart)) {
                $cart->discounted_delivery_fee = 0;
                $cart->delivery_fee_discount_type = \App\Enums\OrderDiscount\Type::ReservationDeliveryFee;

                return $cart;
            }
        }

        $cart->discounted_delivery_fee = $this->deliverySettingRepository->calculateDeliveryFee($cart->total_item_price_before_order);

        if ($cart->discounted_delivery_fee < $cart->delivery_fee) {
            $cart->delivery_fee_discount_type = \App\Enums\OrderDiscount\Type::DeliveryFee;
        }

        return $cart;
    }

    /**
     * 送料無料の予約販売が適用されているか判定。
     * NOTE: fillPriceBeforeOrderToCreateNewOrderが実行されているcartを引数に渡す
     *
     * @param Cart $cart
     *
     * @return bool
     */
    private function hasFreeDeliveryReservation(Cart $cart)
    {
        $items = $cart->getSecuredCartItems()->map(function ($cartItem) {
            return $cartItem->itemDetail->item;
        });
        $items->load('appliedReservation');

        foreach ($items as $item) {
            if (!empty($item->appliedReservation) && $item->appliedReservation->is_free_delivery) {
                return true;
            }
        }

        return false;
    }

    /**
     * 適用されたキャンペーン（イベント）の最終的な割引金額
     * NOTE: fillPriceBeforeOrderToCreateNewOrderが実行されているcartを引数に渡す
     *
     * @param Cart $cart
     *
     * @return int
     */
    private function calculateCampaignDiscountTotalPrice(Cart $cart)
    {
        $discounts = [];

        foreach ($cart->getSecuredCartItems() as $cartItem) {
            $item = $cartItem->itemDetail->item;

            if ($item->displayed_discount_type === \App\Enums\Item\DiscountType::Event) {
                $discounts[] = ($item->retail_price - $item->displayed_sale_price) * $cartItem->count;
            }

            if (!empty($item->appliedBundleSale)) {
                $discounts[] = ($item->displayed_sale_price - $item->bundle_sale_price) * $cartItem->count;
            }
        }

        return array_sum($discounts);
    }

    /**
     * 適用された社員割引の最終的な割引金額
     * NOTE: fillPriceBeforeOrderToCreateNewOrderが実行されているcartを引数に渡す
     *
     * @param Cart $cart
     *
     * @return int
     */
    private function calculateEmployeeDiscountTotalPrice(Cart $cart)
    {
        $discounts = [];

        foreach ($cart->getSecuredCartItems() as $cartItem) {
            $item = $cartItem->itemDetail->item;

            if ($item->displayed_discount_type === \App\Enums\Item\DiscountType::Staff) {
                $discounts[] = ($item->retail_price - $item->displayed_sale_price) * $cartItem->count;
            }
        }

        return array_sum($discounts);
    }

    /**
     * カートや注文内容確認時に表示する商品金額合計
     * NOTE: fillPriceBeforeOrderToCreateNewOrderが実行されているcartを引数に渡す
     *
     * @param Cart $cart
     *
     * @return int
     */
    private function calculateItemTotalPriceInOrderContent(Cart $cart)
    {
        $price = 0;

        foreach ($cart->getSecuredCartItems() as $cartItem) {
            $item = $cartItem->itemDetail->item;

            switch ($item->displayed_discount_type) {
                case \App\Enums\Item\DiscountType::Normal:
                case \App\Enums\Item\DiscountType::Member:
                case \App\Enums\Item\DiscountType::Reservation:
                    $price += ($item->displayed_sale_price * $cartItem->count);
                    break;

                default:
                    $price += ($item->retail_price * $cartItem->count);
                    break;
            }
        }

        return $price;
    }

    /**
     * @param array $params
     * @param int $totalAmount
     * @param string $accessToken
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function amazonPayOrderConfirm(array $params, int $totalAmount, string $accessToken)
    {
        $orderReferenceDetails = $this->amazonPayService->orderConfirm(
            $params['amazon_order_reference_id'],
            $totalAmount,
            $accessToken
        );

        return $orderReferenceDetails;
    }
}
