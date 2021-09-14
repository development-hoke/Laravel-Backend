<?php

namespace App\Domain\Adapters\Ymdy;

use App\Domain\CouponInterface as DomainCouponService;
use App\Domain\ItemPriceInterface as ItemPrice;
use App\Entities\Ymdy\Member\EcBill;
use App\Entities\Ymdy\Member\EcColor;
use App\Entities\Ymdy\Member\EcDetail;
use App\Entities\Ymdy\Member\EcDiscount;
use App\Entities\Ymdy\Member\EcPaymentType;
use App\Entities\Ymdy\Member\EcSize;
use App\Exceptions\FatalException;
use App\HttpCommunication\Ymdy\PurchaseInterface as PurchaseHttpCommunication;
use App\Utils\Arr;
use App\Utils\Tax;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;

class CartMemberPurchase implements CartMemberPurchaseInterface
{
    /**
     * @var PurchaseHttpCommunication
     */
    private $purchaseHttpCommunication;

    /**
     * @var DomainCouponService
     */
    private $domainCouponService;

    /**
     * @var ItemPrice
     */
    private $itemPrice;

    /**
     * @param PurchaseHttpCommunication $purchaseHttpCommunication
     * @param DomainCouponService $domainCouponService
     * @param ItemPrice $itemPrice
     */
    public function __construct(
        PurchaseHttpCommunication $purchaseHttpCommunication,
        DomainCouponService $domainCouponService,
        ItemPrice $itemPrice
    ) {
        $this->purchaseHttpCommunication = $purchaseHttpCommunication;
        $this->domainCouponService = $domainCouponService;
        $this->itemPrice = $itemPrice;
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
        $this->purchaseHttpCommunication->setMemberTokenHeader($token);

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
        $this->purchaseHttpCommunication->setStaffToken($token);

        return $this;
    }

    /**
     * 付与ポイントの計算
     *
     * @param \App\Models\Order $order
     *
     * @return array
     */
    public function calculatePoint(\App\Models\Cart $cart, array $prices, array $options = [])
    {
        // // エラーが返ってくるためモックを返す
        // return [
        //     'base_grant_point' => 0,
        //     'special_grant_point' => 0,
        //     'effective_point' => 0,
        // ];

        $cart = $cart->replicateWithKey();

        $ecBill = $this->makeEcBill($cart, $prices, $options);

        $response = $this->purchaseHttpCommunication->calculatePoint($cart->member_id, $ecBill->toArray());

        return $response->getBody();
    }

    /**
     * @param \App\Models\Cart $order
     *
     * @return EcBill
     */
    private function makeEcBill(\App\Models\Cart $cart, array $prices, array $options = [])
    {
        $cart = $this->itemPrice->fillPriceBeforeOrderToCreateNewOrder($cart);

        $portionedPoints = $this->portionPoints($cart, $options['use_point'] ?? 0);
        $portionedCoupons = $this->portionCoupons($cart);

        $ecDetails = $this->createEcDetails($cart, $portionedPoints, $portionedCoupons);
        $ecBill = $this->createEcBill($cart, $ecDetails, $prices, $options);

        return $ecBill;
    }

    /**
     * @param \App\Models\Cart $cart
     * @param \App\Entities\Collection $ecDetails
     * @param array $prices
     * @param array $options
     *
     * @return EcBill
     */
    private function createEcBill(\App\Models\Cart $cart, \App\Entities\Collection $ecDetails, array $prices, array $options = [])
    {
        $ecBill = new EcBill();

        if (isset($options['delivery_hope_date'])) {
            $ecBill->delivery_date = Carbon::parse($options['delivery_hope_date'])->format('Y-m-d');
        }

        $ecBill->total_price = $prices['total_price'];
        $ecBill->delivery_fee = $cart->discounted_delivery_fee;
        $ecBill->delivery_fee_memo = \App\Enums\OrderDiscount\Type::getDescription($cart->delivery_fee_discount_type);
        $ecBill->payment_fee = $prices['payment_fee'];
        $ecBill->total_tax = $ecDetails->sum('tax')
            + Tax::calcTax($cart->discounted_delivery_fee)
            + Tax::calcTax($prices['payment_fee']);
        $ecBill->tax_excluded_total_price = $prices['total_price'] - $ecBill->total_tax;
        $ecBill->detail_num = $ecDetails->count();
        $ecBill->details = $ecDetails;

        if (isset($options['payment_type'])) {
            $paymentType = new EcPaymentType();
            $paymentType->id = $options['payment_type'];
            $paymentType->name = \App\Enums\Order\PaymentType::getDescription($options['payment_type']);
            $ecBill->payment = $paymentType;
        }

        return $ecBill;
    }

    /**
     * @param \App\Models\Cart $cart
     * @param SupportCollection $usedPoints
     * @param array $portionedCoupons
     *
     * @return \App\Entities\Collection
     */
    private function createEcDetails(\App\Models\Cart $cart, SupportCollection $usedPoints, array $portionedCoupons)
    {
        $ecDetails = EcDetail::collection();
        $usedPoints = Arr::dict($usedPoints, 'cart_item_id');

        foreach ($cart->getSecuredCartItems() as $cartItem) {
            $itemDetail = $cartItem->itemDetail;
            $item = $itemDetail->item;
            $itemDetailIdentCandidate = $itemDetail->itemDetailIdentifications->sortBy('arrival_date')->first();

            $usedPoint = $usedPoints[$cartItem->id]['price'] ?? 0;
            $usedCoupons = $portionedCoupons[$cartItem->id] ?? collect();

            $salePrice = ($item->price_before_order * $cartItem->count)
                - $usedPoint
                - $usedCoupons->sum('price');
            $tax = Tax::calcTax($salePrice);

            $ecDetail = new EcDetail();
            $ecDetail->id = $cartItem->id;
            $ecDetail->item_jan_code = $itemDetailIdentCandidate->jan_code;

            $ecDetail->retail_price = $item->retail_price;
            $ecDetail->sales_price = $salePrice;
            $ecDetail->amount = $cartItem->count;
            $ecDetail->use_point = $usedPoint > 0 ? $usedPoint : null;

            $ecDetail->pb_div = $this->getPbDiv($item, $usedCoupons);
            $ecDetail->crosspoint_pb_div = $this->getCrosspointPbDiv($item, $usedCoupons);

            $ecDetail->tax_rate_id = Tax::getDefaultTaxId();
            $ecDetail->tax_type = \App\Enums\Ymdy\Member\TaxType::Included;
            $ecDetail->tax = $tax;
            $ecDetail->tax_excluded_sale_price = $salePrice - $tax;
            $ecDetail->item_published_date = $item->sales_period_from;

            $ecColor = new EcColor();
            $ecColor->id = $itemDetail->color_id;
            $ecColor->code = $itemDetail->color->code;
            $ecColor->name = $itemDetail->color->name;
            $ecColor->display_name = $itemDetail->color->display_name;
            $ecDetail->color = $ecColor;

            $ecSize = new EcSize();
            $ecSize->id = $itemDetail->size_id;
            $ecSize->code = $itemDetail->size->code;
            $ecSize->search_code = $itemDetail->size->search_code;
            $ecSize->name = $itemDetail->size->name;
            $ecDetail->size = $ecSize;

            $ecDetail->discounts = EcDiscount::collection([]);

            if ($item->displayed_discount_type !== \App\Enums\Item\DiscountType::None) {
                $discount = new EcDiscount();
                // IDに該当するものがないのでブランクで送る
                // $discount->id = $orderDiscount->id;
                $discount->type = $this->convertEcDiscountTypeToMemberDiscountType($item->displayed_discount_type);
                $discount->price = $cartItem->count > 0
                    ? ($item->retail_price - $item->displayed_sale_price) * $cartItem->count
                    : 0;

                $ecDetail->discounts->add($discount);
            }

            if (!empty($item->appliedBundleSale)) {
                $discount = new EcDiscount();
                $discount->type = \App\Enums\Ymdy\Member\Purchase\DiscountType::EventBundle;
                $discount->price = ($item->displayed_sale_price - $item->bundle_sale_price) * $cartItem->count;
                $ecDetail->discounts->add($discount);
            }

            foreach ($usedCoupons as $usedCoupon) {
                $discount = new EcDiscount();
                $discount->type = \App\Enums\Ymdy\Member\Purchase\DiscountType::Coupon;
                $discount->price = $usedCoupon['price'];
                $discount->coupon_id = $usedCoupon['coupon']['coupon_id'];

                $ecDetail->discounts->add($discount);
            }

            $ecDetails->add($ecDetail);
        }

        return $ecDetails;
    }

    /**
     * @param int $type
     *
     * @return int
     *
     * @throws FatalException
     */
    private static function convertEcDiscountTypeToMemberDiscountType($type)
    {
        switch ((int) $type) {
            case \App\Enums\Item\DiscountType::Normal:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::Normal;
            case \App\Enums\Item\DiscountType::Member:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::Member;
            case \App\Enums\Item\DiscountType::Staff:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::Staff;
            case \App\Enums\Item\DiscountType::Event:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::EventSale;
            case \App\Enums\OrderDiscount\Type::Reservation:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::Reservation;

            default:
                throw new FatalException(error_format('error.invalid_argument_value', [__METHOD__, 'type' => $type]));
        }
    }

    /**
     * P/B区分の取得。区分はクーポンの按分結果に依存する。
     *
     * @param \App\Models\Item $item
     * @param SupportCollection $coupons
     *
     * @return int \App\Enums\Ymdy\Member\PvDiv
     */
    private static function getPbDiv(\App\Models\Item $item, SupportCollection $portionedCoupons)
    {
        if ($portionedCoupons->isNotEmpty()) {
            return \App\Enums\Ymdy\Member\PvDiv::Bargain;
        }

        $saleType = \App\Domain\Utils\OrderSaleType::getSaleTypeByItem($item);

        return $saleType === \App\Enums\Order\SaleType::Employee
            ? \App\Enums\Ymdy\Member\PvDiv::Proper
            : \App\Enums\Ymdy\Member\PvDiv::Bargain;
    }

    /**
     * @param \App\Models\Item $item
     *
     * @return string \App\Enums\Ymdy\Member\CrosspointPvDiv
     */
    private static function getCrosspointPbDiv(\App\Models\Item $item, SupportCollection $portionedCoupons)
    {
        if ($portionedCoupons->isNotEmpty()) {
            return \App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain;
        }

        switch ((int) $item->displayed_discount_type) {
            case \App\Enums\Item\DiscountType::Normal:
            case \App\Enums\Item\DiscountType::Member:
            case \App\Enums\Item\DiscountType::Staff:
            case \App\Enums\Item\DiscountType::Event:
                return \App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain;

            default:
                break;
        }

        return !empty($item->appliedBundleSale)
            ? \App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain
            : \App\Enums\Ymdy\Member\CrosspointPvDiv::Proper;
    }

    /**
     * ポイント按分計算
     *
     * @param \App\Models\Cart $cart
     * @param int $usePoint
     *
     * @return \Illuminate\Support\Collection
     */
    private function portionPoints(\App\Models\Cart $cart, int $usePoint)
    {
        if ((int) $usePoint === 0) {
            return collect([]);
        }

        return $this->portion($cart->getSecuredCartItems(), $usePoint);
    }

    /**
     * クーポン按分計算
     *
     * @param \App\Models\Cart $cart
     *
     * @return array
     */
    private function portionCoupons(\App\Models\Cart $cart)
    {
        $portioned = [];

        if ($cart->memberCoupons->isEmpty()) {
            return $portioned;
        }

        $coupons = $cart->memberCoupons->pluck('coupon');

        foreach ($coupons as $coupon) {
            if (!$coupon->discount_item_flag) {
                continue;
            }

            $targetCartItems = $this->getPortioningCouponTargets(
                $cart->getSecuredCartItems(),
                $coupon
            );

            $this->domainCouponService->fillDiscountPriceToCoupon($coupon, $cart);

            $portion = $this->portion($targetCartItems, $coupon->discount_price);

            foreach ($portion as $data) {
                if (!isset($portioned[$data['cart_item_id']])) {
                    $portioned[$data['cart_item_id']] = collect([]);
                }

                $portioned[$data['cart_item_id']]->add(array_merge($data, [
                    'coupon' => $coupon,
                ]));
            }
        }

        return $portioned;
    }

    /**
     * @param Collection $orderDetails
     * @param \App\Entities\Ymdy\Member\Coupon $coupon
     *
     * @return Collection
     */
    private function getPortioningCouponTargets(
        Collection $cartItems,
        \App\Entities\Ymdy\Member\Coupon $coupon
    ) {
        if ($coupon->target_item_type === \App\Enums\Coupon\TargetItemType::All) {
            return $cartItems;
        }

        $itemDict = Arr::dict($coupon->_item_cd_data);

        return $cartItems->filter(function ($cartItem) use ($itemDict) {
            $itemDetailIdentifications = $cartItem->itemDetail->itemDetailIdentifications;

            foreach ($itemDetailIdentifications as $ident) {
                if (isset($itemDict[$ident->jan_code])) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @param int $discountPrice
     *
     * @return \Illuminate\Support\Collection
     */
    private static function portion(\Illuminate\Database\Eloquent\Collection $cartItems, int $discountPrice)
    {
        $totalPrice = $cartItems->map(function ($cartItem) {
            return $cartItem->itemDetail->item->price_before_order * $cartItem->count;
        })->sum();

        $lastIndex = $cartItems->count() - 1;
        $proportion = collect([]);

        foreach (array_values($cartItems->all()) as $i => $cartItem) {
            $item = $cartItem->itemDetail->item;

            if ($lastIndex > $i) {
                $price = (int) floor((($item->price_before_order * $cartItem->count) / $totalPrice) * $discountPrice);
            } else {
                $price = (int) ($discountPrice - $proportion->sum('price'));
            }

            $proportion->add([
                'price' => $price,
                'cart_item_id' => $cartItem->id,
            ]);
        }

        return $proportion;
    }
}
