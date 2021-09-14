<?php

namespace App\Domain;

use App\Entities\Ymdy\Member\Coupon as CouponEntity;
use App\Entities\Ymdy\Member\MemberCoupon as MemberCouponEntity;
use App\Exceptions\FailedAddingCouponException;
use App\Exceptions\InvalidInputException;
use App\HttpCommunication\Ymdy\MemberInterface as MemberHttpCommunicationService;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDiscount;
use App\Models\OrderUsedCoupon;
use App\Repositories\OrderDiscountRepository;
use App\Repositories\OrderUsedCouponRepository;
use App\Utils\Arr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class Coupon implements CouponInterface
{
    /**
     * @var MemberHttpCommunicationService
     */
    private $memberHttpCommunication;

    /**
     * @var OrderDiscountRepository
     */
    private $orderDiscountRepository;

    /**
     * @var OrderUsedCouponRepository
     */
    private $orderUsedCouponRepository;

    /**
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        MemberHttpCommunicationService $memberHttpCommunication,
        OrderDiscountRepository $orderDiscountRepository,
        OrderUsedCouponRepository $orderUsedCouponRepository
    ) {
        $this->memberHttpCommunication = $memberHttpCommunication;
        $this->orderDiscountRepository = $orderDiscountRepository;
        $this->orderUsedCouponRepository = $orderUsedCouponRepository;
    }

    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberToken(string $token)
    {
        $this->memberHttpCommunication->setMemberTokenHeader($token);

        return $this;
    }

    /**
     * スタッフトークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token)
    {
        $this->memberHttpCommunication->setStaffToken($token);

        return $this;
    }

    /**
     * クーポン取得
     *
     * @param int $id
     *
     * @return CouponEntity
     */
    public function fetchCoupon(int $id)
    {
        $data = $this->memberHttpCommunication->showCoupon($id)->getBody();

        return new CouponEntity($data['coupon']);
    }

    /**
     * クーポンを複数件取得
     *
     * @param int|int[] $id
     *
     * @return \App\Entities\Collection
     */
    public function fetchCouponsByIds($id)
    {
        $coupons = [];

        foreach ((array) $id as $couponId) {
            $coupons[] = $this->fetchCoupon($couponId);
        }

        return CouponEntity::collection($coupons);
    }

    /**
     * @param Collection|\App\Models\OrderUsedCoupon $orderUsedCoupons
     *
     * @return Collection|\App\Models\OrderUsedCoupon
     */
    public function loadCouponToOrderUsedCoupons($orderUsedCoupons)
    {
        $isSingle = $orderUsedCoupons instanceof \App\Models\OrderUsedCoupon;

        if ($isSingle) {
            $orderUsedCoupons = $orderUsedCoupons->newCollection([$orderUsedCoupons]);
        }

        foreach ($orderUsedCoupons as $usedCoupon) {
            $coupon = $this->fetchCoupon($usedCoupon->coupon_id);
            $usedCoupon->setRelation('coupon', $coupon);
        }

        return $isSingle ? $orderUsedCoupons->first() : $orderUsedCoupons;
    }

    /**
     * @param \App\Models\Order $order
     * @param bool|null $skipLoadRelation
     *
     * @return \App\Models\Order
     */
    public function loadUsedCouponsWithDetail(\App\Models\Order $order, ?bool $skipLoadRelation = false)
    {
        !$skipLoadRelation && $order->load('orderUsedCoupons.itemDiscount');

        $this->loadCouponToOrderUsedCoupons($order->orderUsedCoupons);

        return $order;
    }

    /**
     * 利用可能クーポンの検索
     *
     * @param string $memberId
     * @param array|null $query
     * @param array|null $params
     *
     * @return \App\Entities\Collection
     */
    public function searchAvailableMemberCoupon($memberId, ?array $query = [], ?array $params = [])
    {
        $data = $this->memberHttpCommunication->searchAvailableCoupon($memberId, $query, $params)->getBody();

        return MemberCouponEntity::collection($data['member_coupons']);
    }

    /**
     * 利用可能クーポン一覧取得
     *
     * @param string $memberId
     * @param array|null $query
     *
     * @return \App\Entities\Collection
     */
    public function fetchAvailableMemberCoupons(int $memberId, ?array $query = [])
    {
        $data = $this->memberHttpCommunication->getAvailableCoupons($memberId, $query)->getBody();

        return MemberCouponEntity::collection($data['member_coupons']);
    }

    /**
     * 定率割引価格の計算
     *
     * @param float $rate (会員ポイントからは10%の場合、10 で入ってくる)
     *
     * @return float
     */
    public static function calculateDiscountRate(float $rate)
    {
        return $rate / 100;
    }

    /**
     * 定率割引価格の計算
     *
     * @param int $price
     * @param float $rate (会員ポイントからは10%の場合、10 で入ってくる)
     *
     * @return int
     */
    public static function calculateDiscountPriceByRate(int $price, float $rate)
    {
        return (int) floor($price * self::calculateDiscountRate($rate));
    }

    /**
     * order_used_couponsとorder_discountsを複数作成
     *
     * @param array $couponIds
     * @param Order $order
     *
     * @return Collection
     */
    public function addCoupons(array $couponIds, Order $order)
    {
        $orderUsedCoupons = (new OrderUsedCoupon())->newCollection();

        foreach ($couponIds as $couponId) {
            $coupon = $this->fetchCoupon($couponId);

            $created = $this->addCoupon($coupon, $order);

            $orderUsedCoupons->add($created);
        }

        return $orderUsedCoupons;
    }

    /**
     * @param CouponEntity $coupon
     * @param Order $order
     * @param int|null $staffId
     *
     * @return OrderUsedCoupon
     *
     * @throws FailedAddingCouponException
     */
    public function addCoupon(CouponEntity $coupon, Order $order, $staffId = null)
    {
        $order->load([
            'orderDetails.orderDetailUnits',
            'orderDetails.itemDetail.item',
        ]);

        $targetOrderDetailIds = $this->computeTargetOrderDetails($coupon, $order)->pluck('id')->toArray();

        // 適用可能な商品が存在しなかった場合
        if (empty($targetOrderDetailIds)) {
            throw new FailedAddingCouponException('error.no_applicable_discount_coupon');
        }

        $orderUsedCoupon = $this->createOrderUsedCoupon($order, $coupon, $targetOrderDetailIds, $staffId);

        if (\App\Utils\Cast::booleanLike($coupon->discount_item_flag)) {
            $discountPrice = $this->computeItemDiscountPrice($coupon, $order);
            $itemDiscount = $this->orderDiscountRepository->makeModel();
            $itemDiscount = $this->fillItemCouponDiscount($itemDiscount, $coupon, $orderUsedCoupon, $discountPrice, $staffId);
            $itemDiscount->save();
            $orderUsedCoupon->setRelation('itemDiscount', $itemDiscount);
        }

        if (\App\Utils\Cast::booleanLike($coupon->free_shipping_flag)) {
            $deliveryFeeDiscount = $this->createDeliveryFeeCouponOrderDiscount($order, $orderUsedCoupon, $staffId);
            $orderUsedCoupon->setRelation('deliveryFeeDiscount', $deliveryFeeDiscount);
        }

        return $orderUsedCoupon;
    }

    /**
     * @param \App\Models\Order $order
     * @param CouponEntity $coupon
     * @param array $targetOrderDetailIds
     * @param int|null $staffId
     *
     * @return \App\Models\OrderUsedCoupon
     */
    private function createOrderUsedCoupon(\App\Models\Order $order, CouponEntity $coupon, array $targetOrderDetailIds, $staffId = null)
    {
        $orderUsedCoupon = $this->orderUsedCouponRepository->makeModel();
        $orderUsedCoupon->order_id = $order->id;
        $orderUsedCoupon->coupon_id = $coupon->id;
        $orderUsedCoupon->target_order_detail_ids = $targetOrderDetailIds;

        if (isset($staffId)) {
            $orderUsedCoupon->update_staff_id = $staffId;
        }

        $orderUsedCoupon->save();

        return $orderUsedCoupon;
    }

    /**
     * 送料割引のクーポンのorder_discountsの作成
     *
     * @param Order $order
     * @param OrderUsedCoupon $orderUsedCoupon
     * @param int|null $staffId
     *
     * @return \App\Models\OrderDiscount
     */
    private function createDeliveryFeeCouponOrderDiscount(Order $order, OrderUsedCoupon $orderUsedCoupon, $staffId = null)
    {
        $orderDiscount = $this->orderDiscountRepository->makeModel();

        $orderDiscount->orderable_type = get_class($order);
        $orderDiscount->orderable_id = $order->id;
        $orderDiscount->applied_price = $order->delivery_fee;
        $orderDiscount->type = \App\Enums\OrderDiscount\Type::CouponDeliveryFee;
        $orderDiscount->method = \App\Enums\OrderDiscount\Method::Fixed;
        $orderDiscount->discountable_type = get_class($orderUsedCoupon);
        $orderDiscount->discountable_id = $orderUsedCoupon->id;
        $orderDiscount->discount_price = $order->delivery_fee;

        if (isset($staffId)) {
            $orderDiscount->update_staff_id = $staffId;
        }

        $orderDiscount->save();

        return $orderDiscount;
    }

    /**
     * @param OrderDiscount $orderDiscount
     * @param CouponEntity $coupon
     * @param OrderUsedCoupon $orderUsedCoupon
     * @param int $discountPrice
     * @param int|null $staffId
     *
     * @return OrderDiscount
     */
    private function fillItemCouponDiscount(
        OrderDiscount $orderDiscount,
        CouponEntity $coupon,
        OrderUsedCoupon $orderUsedCoupon,
        int $discountPrice,
        $staffId = null
    ) {
        $method = \App\Domain\Utils\OrderDiscount::getMethodByCouponDiscountType($coupon->discount_type);

        $orderDiscount->orderable_type = \App\Models\Order::class;
        $orderDiscount->orderable_id = $orderUsedCoupon->order_id;
        $orderDiscount->applied_price = $discountPrice;
        $orderDiscount->type = \App\Enums\OrderDiscount\Type::CouponItem;
        $orderDiscount->method = $method;
        $orderDiscount->discountable_type = get_class($orderUsedCoupon);
        $orderDiscount->discountable_id = $orderUsedCoupon->id;

        if ((int) $coupon->discount_type === \App\Enums\Coupon\DiscountType::Fixed) {
            $orderDiscount->discount_price = $discountPrice;
        } else {
            $orderDiscount->discount_rate = self::calculateDiscountRate($coupon->discount_rate);
        }

        if (isset($staffId)) {
            $orderDiscount->update_staff_id = $staffId;
        }

        return $orderDiscount;
    }

    /**
     * @param CouponEntity $coupon
     * @param Order $order
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function computeTargetOrderDetails(CouponEntity $coupon, Order $order)
    {
        if ((int) $coupon->target_item_type === \App\Enums\Coupon\TargetItemType::All) {
            return $order->orderDetails;
        }

        $dict = Arr::dict($coupon->_item_cd_data);
        $targetOrderDetails = (new OrderDetail())->newCollection();

        foreach ($order->orderDetails as $orderDetail) {
            if ($orderDetail->amount === 0) {
                continue;
            }

            foreach ($orderDetail->orderDetailUnits as $orderDetailUnit) {
                if (!isset($dict[$orderDetailUnit->itemDetailIdentification->jan_code])) {
                    continue 2;
                }
            }

            $targetOrderDetails->add($orderDetail);
        }

        return $targetOrderDetails;
    }

    /**
     * 割引金額と対象受注詳細を返却する
     *
     * @param \App\Entities\Ymdy\Member\Coupon $coupon
     * @param \App\Models\Order $order
     *
     * @return int
     */
    private function computeItemDiscountPrice(CouponEntity $coupon, Order $order)
    {
        if ((int) $coupon->discount_type === \App\Enums\Coupon\DiscountType::Fixed) {
            return (int) $coupon->discount_amount;
        }

        $targetOrderDetails = $this->computeTargetOrderDetails($coupon, $order);

        return $this->calculateDiscountPriceByRate(
            $targetOrderDetails->sum('total_price_before_order'),
            $coupon->discount_rate
        );
    }

    /**
     * クーポン削除
     *
     * @param int $orderUsedCouponId
     * @param int|null $staffId
     *
     * @return \App\Models\OrderUsedCoupon
     */
    public function removeCoupon($orderUsedCouponId, $staffId = null)
    {
        $usedCoupon = $this->orderUsedCouponRepository->find($orderUsedCouponId);

        $usedCoupon->deleteRelatedItemOrderDiscount($staffId);

        $usedCoupon->deleteRelatedDeliveryFeeOrderDiscount($staffId);

        $usedCoupon->softDeleteBy($staffId);

        return $usedCoupon;
    }

    /**
     * orderに紐づくクーポンの状態を更新する
     * - 対象受注詳細IDの更新
     * - 値に変更があればクーポン情報を更新
     * - 対象商品が0件だった場合order_discountを削除
     * - 対象商品が0件かつ送料割引ではなかった場合order_used_couponを削除
     *
     * @param \App\Models\Order $order
     * @param int|null $staffId
     *
     * @return \App\Models\Order
     */
    public function updateOrderRelatedCouponState(\App\Models\Order $order, ?int $staffId = null)
    {
        $order->load([
            'orderDetails.orderDetailUnits',
            'orderDetails.itemDetail.item',
            'orderUsedCoupons.itemDiscount',
            'orderUsedCoupons.deliveryFeeDiscount',
        ]);

        if (!$order->orderUsedCoupons->isEmpty()) {
            $order->orderUsedCoupons->each(function ($orderUsedCoupon) use ($order, $staffId) {
                $this->updateItemCouponOrderDiscount($orderUsedCoupon, $order, $staffId);

                if ((empty($orderUsedCoupon->itemDiscount) && empty($orderUsedCoupon->deliveryFeeDiscount))
                    || empty($orderUsedCoupon->target_order_detail_ids)
                ) {
                    $orderUsedCoupon->softDeleteBy($staffId);
                    $order->setRelation(
                        'orderUsedCoupons',
                        $order->orderUsedCoupons->where('id', '!=', $orderUsedCoupon->id)
                    );
                }
            });
        }

        return $order;
    }

    /**
     * @param OrderUsedCoupon $orderUsedCoupon
     * @param Order $order
     * @param int $staffId
     *
     * @return OrderUsedCoupon
     */
    private function updateItemCouponOrderDiscount(OrderUsedCoupon $orderUsedCoupon, Order $order, $staffId = null)
    {
        $coupon = $this->fetchCoupon($orderUsedCoupon->coupon_id);
        $discountPrice = $this->computeItemDiscountPrice($coupon, $order);

        if ($discountPrice === 0) {
            return $orderUsedCoupon->deleteRelatedItemOrderDiscount($staffId);
        }

        // 適用対象となった受注詳細IDを更新
        $orderUsedCoupon->target_order_detail_ids = $this->computeTargetOrderDetails($coupon, $order)->pluck('id')->toArray();
        $orderUsedCoupon->save();

        // 関連レコードを作成
        $itemDiscount = $orderUsedCoupon->itemDiscount ?? $this->orderDiscountRepository->makeModel();
        $itemDiscount = $this->fillItemCouponDiscount($itemDiscount, $coupon, $orderUsedCoupon, $discountPrice, $staffId);
        $itemDiscount->save();

        $orderUsedCoupon->setRelation('itemDiscount', $itemDiscount);

        return $orderUsedCoupon;
    }

    /**
     * 検証済みクーポンの取得
     *
     * @param int $memberId
     * @param array $couponIds
     * @param \App\Models\Cart $cart
     *
     * @return \App\Entities\Collection|\App\Entities\Ymdy\Member\MemberCoupon[]
     *
     * @throws InvalidInputException
     */
    public function fetchValidatedMemberCoupons(int $memberId, array $couponIds, \App\Models\Cart $cart)
    {
        // (1) 利用可能クーポンに含まれているか、最新の値で判定
        $availableCoupons = $this->fetchAvailableMemberCoupons($memberId);
        $dict = Arr::dict($availableCoupons, 'coupon_id');
        $validatingCoupons = [];

        foreach ($couponIds as $couponId) {
            if (!isset($dict[$couponId])) {
                throw new InvalidInputException(__('validation.coupon.invalid_request'));
            }

            $validatingCoupons[] = $dict[$couponId];
        }

        // (2) 利用条件に一致しているかを確認
        $validatedCoupons = $this->filterCartMemberCoupons($validatingCoupons, $cart, true);

        // (3) 複数の場合併用可能か判定
        $this->validateMultipleUse($validatedCoupons);

        return $validatedCoupons;
    }

    /**
     * 併用可能かチェック
     *
     * @param \App\Entities\Collection|\App\Entities\Ymdy\Member\MemberCoupon[] $memberCoupons
     *
     * @return void
     */
    private function validateMultipleUse(\App\Entities\Collection $memberCoupons)
    {
        if ($memberCoupons->count() < 2) {
            return;
        }

        $hasUncombinable = false;

        foreach ($memberCoupons as $memberCoupon) {
            if (!((int) $memberCoupon->coupon->is_combinable)) {
                if ($hasUncombinable) {
                    throw new InvalidInputException(__('validation.coupon.multiple_use'));
                }

                $hasUncombinable = true;
            }
        }

        return;
    }

    /**
     * 現在のカートの状況から利用可能なクーポンを取得する
     *
     * @param \App\Models\Cart $cart
     *
     * @return \App\Entities\Collection|\App\Entities\Ymdy\Member\MemberCoupon[]
     */
    public function fetchAvailableCartMemberCoupons(\App\Models\Cart $cart)
    {
        if (empty($cart->member_id) || $cart->is_guest) {
            return \App\Entities\Ymdy\Member\MemberCoupon::collection();
        }

        $memberCoupons = $this->fetchAvailableMemberCoupons($cart->member_id);

        $memberCoupons = $this->filterCartMemberCoupons($memberCoupons, $cart);

        $this->calculateCartCouponDiscount($cart->replicate(), $memberCoupons);

        return $memberCoupons;
    }

    /**
     * 現在のカートの状況から利用可能なクーポンのみ取得する
     *
     * @param mixed $memberCoupons
     * @param \App\Models\Cart $cart
     * @param bool|null $throwException
     *
     * @return \App\Entities\Collection
     */
    private function filterCartMemberCoupons($memberCoupons, \App\Models\Cart $cart, ?bool $throwException = false)
    {
        $now = Carbon::now();

        $janCodeDict = [];

        foreach ($cart->getSecuredCartItems() as $item) {
            foreach ($item->itemDetail->itemDetailIdentifications as $ident) {
                $janCodeDict[$ident->jan_code] = $ident->jan_code;
            }
        }

        $filtered = [];

        foreach ($memberCoupons as $memberCoupon) {
            $coupon = $memberCoupon->coupon;

            // (1) 購入金額の下限、上限チェック
            if ($coupon->usage_amount_term_flag) {
                if (isset($coupon->usage_amount_minimum) && $coupon->usage_amount_minimum > $cart->total_item_price_before_order) {
                    if (!$throwException) {
                        continue;
                    }

                    throw new InvalidInputException(__('validation.coupon.usage_amount_minimum', [
                        'name' => $coupon->name,
                        'value' => number_format($coupon->usage_amount_minimum),
                    ]));
                }

                if (isset($coupon->usage_amount_maximum) && $coupon->usage_amount_maximum < $cart->total_item_price_before_order) {
                    if (!$throwException) {
                        continue;
                    }

                    throw new InvalidInputException(__('validation.coupon.usage_amount_maximum', [
                        'name' => $coupon->name,
                        'value' => number_format($coupon->usage_amount_maximum),
                    ]));
                }
            }

            // (2) 利用可能期間のチェック
            if (!empty($coupon->start_dt) && $now->lt($coupon->start_dt)) {
                if (!$throwException) {
                    continue;
                }

                throw new InvalidInputException(__('validation.coupon.start_dt', ['name' => $coupon->name]));
            }

            if (!empty($coupon->end_dt) && $now->gt($coupon->end_dt)) {
                if (!$throwException) {
                    continue;
                }

                throw new InvalidInputException(__('validation.coupon.end_dt', ['name' => $coupon->name]));
            }

            // (3) 対象商品の確認（_item_cd_dataはJAN）
            if ((int) $coupon->target_item_type !== \App\Enums\Coupon\TargetItemType::All) {
                foreach ($coupon->_item_cd_data as $jan) {
                    if ($found = isset($janCodeDict[$jan])) {
                        break;
                    }
                }

                if (!$found) {
                    if (!$throwException) {
                        continue;
                    }

                    throw new InvalidInputException(__('validation.coupon.out_of_target_item', ['name' => $coupon->name]));
                }
            }

            $filtered[] = $memberCoupon;
        }

        return \App\Entities\Ymdy\Member\MemberCoupon::collection($filtered);
    }

    /**
     * クーポン値引の計算
     *
     * @param \App\Models\Cart $cart
     * @param \App\Entities\Collection|null $memberCoupons
     *
     * @return \App\Models\Cart
     */
    public function calculateCartCouponDiscount(\App\Models\Cart $cart, $memberCoupons = null)
    {
        $memberCoupons = $memberCoupons ?? $cart->memberCoupons;

        foreach ($memberCoupons as $memberCoupon) {
            $coupon = $memberCoupon->coupon;

            $this->fillDiscountPriceToCoupon($coupon, $cart);

            $this->fillHasFreeShippingCouponToCart($cart, $coupon);
        }

        return $cart;
    }

    /**
     * @param \App\Entities\Ymdy\Member\Coupon $coupon
     * @param \App\Models\Cart $cart
     *
     * @return \App\Entities\Ymdy\Member\Coupon
     */
    public static function fillDiscountPriceToCoupon(\App\Entities\Ymdy\Member\Coupon $coupon, \App\Models\Cart $cart)
    {
        if ((int) $coupon->discount_item_flag) {
            switch ((int) $coupon->discount_type) {
                case \App\Enums\Coupon\DiscountType::Fixed:
                    $coupon->discount_price = $coupon->discount_amount;
                    break;
                case \App\Enums\Coupon\DiscountType::Percentile:
                    $coupon->discount_price = static::computePercentileMethodDiscountPriceFromCart($cart, $coupon);
                    break;
            }
        }

        return $coupon;
    }

    /**
     * @param \App\Models\Cart $cart
     * @param \App\Entities\Ymdy\Member\Coupon $coupon
     *
     * @return \App\Models\Cart
     */
    public static function fillHasFreeShippingCouponToCart(\App\Models\Cart $cart, \App\Entities\Ymdy\Member\Coupon $coupon)
    {
        if ((int) $coupon->free_shipping_flag) {
            $cart->has_free_shipping_coupon = true;
        }

        return $cart;
    }

    /**
     * @param \App\Models\Cart $cart
     * @param CouponEntity $coupon
     *
     * @return int
     */
    private static function computePercentileMethodDiscountPriceFromCart(\App\Models\Cart $cart, CouponEntity $coupon)
    {
        $cartItems = $cart->getSecuredCartItems();

        if ((int) $coupon->target_item_type === \App\Enums\Coupon\TargetItemType::All) {
            $price = $cartItems->map(function ($cartItem) {
                return $cartItem->itemDetail->item->price_before_order * $cartItem->count;
            })->sum();

            return static::calculateDiscountPriceByRate($price, $coupon->discount_rate);
        }

        $targetJans = Arr::dict($coupon->_item_cd_data);
        $targetItemPrices = [];

        foreach ($cartItems as $cartItem) {
            $itemDetail = $cartItem->item_detail;

            foreach ($itemDetail->itemDetailIdentifications as $ident) {
                if (isset($targetJans[$ident->jan_code])) {
                    $targetItemPrices[$itemDetail->item_id] = $itemDetail->item->price_before_order * $cartItem->count;
                }
            }
        }

        return static::calculateDiscountPriceByRate(array_sum($targetItemPrices), $coupon->discount_rate);
    }

    /**
     * クーポンの詳細テキスト
     *
     * @param mixed $coupon
     *
     * @return string
     */
    public static function getDiscountText($coupon)
    {
        if ($coupon['discount_item_flag'] === \App\Enums\Coupon\DiscountItemFlag::ItemDiscount) {
            return !empty($coupon['discount_rate']) ? '商品合計金額の'.$coupon['discount_rate'] .'%分' : $coupon['discount_amount'].'円分';
        }

        return '送料無料';
    }

    /**
     * @param mixed $coupon
     *
     * @return string
     */
    public static function getUsageCondition($coupon)
    {
        if ($coupon['usage_amount_term_flag']) {
            $usageAmountMin = $coupon['usage_amount_minimum'];
            $usageAmountMax = $coupon['usage_amount_maximum'];

            if ($usageAmountMin > 0 && $usageAmountMax > 0) {
                return number_format($usageAmountMin) . '円以上' . number_format($usageAmountMax) . '円以下の購買の場合に利用できます。';
            }

            if ($usageAmountMin > 0) {
                return number_format($usageAmountMin) . '円以上購買の場合に利用できます。';
            }

            if ($usageAmountMax > 0) {
                return number_format($usageAmountMax) . '円以下購買の場合に利用できます。';
            }
        }

        return '';
    }
}
