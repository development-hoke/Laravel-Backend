<?php

namespace App\Domain;

use App\Domain\Contracts\AssignableCrediencalToken;
use App\Entities\Ymdy\Member\Coupon as CouponEntity;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface CouponInterface extends AssignableCrediencalToken
{
    /**
     * クーポン取得
     *
     * @param int $id
     *
     * @return \App\Entities\Ymdy\Member\Coupon
     */
    public function fetchCoupon(int $id);

    /**
     * クーポンを複数件取得
     *
     * @param int|int[] $id
     *
     * @return \App\Entities\Collection
     */
    public function fetchCouponsByIds($id);

    /**
     * @param Collection|\App\Models\OrderUsedCoupon $orderUsedCoupons
     *
     * @return Collection|\App\Models\OrderUsedCoupon
     */
    public function loadCouponToOrderUsedCoupons($orderUsedCoupons);

    /**
     * @param \App\Models\Order $order
     * @param bool|null $skipLoadRelation
     *
     * @return \App\Models\Order
     */
    public function loadUsedCouponsWithDetail(\App\Models\Order $order, ?bool $skipLoadRelation = false);

    /**
     * 利用可能クーポンの検索
     *
     * @param string $memberId
     * @param array|null $query
     * @param array|null $params
     *
     * @return \App\Entities\Collection
     */
    public function searchAvailableMemberCoupon($memberId, ?array $query = [], ?array $params = []);

    /**
     * 利用可能クーポン一覧取得
     *
     * @param string $memberId
     * @param array|null $query
     *
     * @return \App\Entities\Collection
     */
    public function fetchAvailableMemberCoupons(int $memberId, ?array $query = []);

    /**
     * order_used_couponsとorder_discountsを複数作成
     *
     * @param array $couponIds
     * @param Order $order
     *
     * @return Collection
     */
    public function addCoupons(array $couponIds, Order $order);

    /**
     * order_discountsの作成
     *
     * @param CouponEntity $coupon
     * @param Order $order
     * @param int|null $staffId
     *
     * @return \App\Models\OrderUsedCoupon
     */
    public function addCoupon(CouponEntity $coupon, Order $order, $staffId = null);

    /**
     * クーポン削除
     *
     * @param int $orderUsedCouponId
     * @param int|null $staffId
     *
     * @return \App\Models\OrderUsedCoupon
     */
    public function removeCoupon($orderUsedCouponId, $staffId = null);

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
    public function updateOrderRelatedCouponState(\App\Models\Order $order, ?int $staffId = null);

    /**
     * 検証済みクーポンの取得
     *
     * @param int $memberId
     * @param array $couponIds
     * @param \App\Models\Cart $cart
     *
     * @return \App\Entities\Collection
     *
     * @throws InvalidInputException
     */
    public function fetchValidatedMemberCoupons(int $memberId, array $couponIds, \App\Models\Cart $cart);

    /**
     * 現在のカートの状況から利用可能なクーポンを取得する
     *
     * @param \App\Models\Cart $cart
     *
     * @return \App\Entities\Collection
     */
    public function fetchAvailableCartMemberCoupons(\App\Models\Cart $cart);

    /**
     * クーポン値引の計算
     *
     * @param \App\Models\Cart $cart
     * @param \App\Entities\Collection|null $memberCoupons
     *
     * @return \App\Models\Cart
     */
    public function calculateCartCouponDiscount(\App\Models\Cart $cart, $memberCoupons = null);

    /**
     * クーポンの詳細テキスト
     *
     * @param mixed $coupon
     *
     * @return string
     */
    public static function getDiscountText($coupon);

    /**
     * @param \App\Entities\Ymdy\Member\Coupon $coupon
     * @param \App\Models\Cart $cart
     *
     * @return \App\Entities\Ymdy\Member\Coupon
     */
    public static function fillDiscountPriceToCoupon(\App\Entities\Ymdy\Member\Coupon $coupon, \App\Models\Cart $cart);

    /**
     * @param \App\Models\Cart $cart
     * @param \App\Entities\Ymdy\Member\Coupon $coupon
     *
     * @return \App\Models\Cart
     */
    public static function fillHasFreeShippingCouponToCart(\App\Models\Cart $cart, \App\Entities\Ymdy\Member\Coupon $coupon);
}
