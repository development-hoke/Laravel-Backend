<?php

namespace App\Domain\Adapters\Ymdy;

use App\Domain\OrderPortionInterface as OrderPortion;
use App\Entities\Ymdy\Member\EcAddress;
use App\Entities\Ymdy\Member\EcBill;
use App\Entities\Ymdy\Member\EcColor;
use App\Entities\Ymdy\Member\EcDetail;
use App\Entities\Ymdy\Member\EcDiscount;
use App\Entities\Ymdy\Member\EcPaymentType;
use App\Entities\Ymdy\Member\EcSize;
use App\Exceptions\FatalException;
use App\Models\Order as OrderModel;
use App\Models\OrderDetail;
use App\Utils\Arr;
use App\Utils\Tax;
use Illuminate\Support\Collection as SupportCollection;

class Purchase
{
    /**
     * @var OrderPortion
     */
    private $orderPortion;

    /**
     * @param OrderPortion $orderPortion
     */
    public function __construct(OrderPortion $orderPortion)
    {
        $this->orderPortion = $orderPortion;
    }

    /**
     * @param OrderModel $order
     *
     * @return EcBill
     */
    public function makeEcBill(OrderModel $order)
    {
        $order = $order->replicateWithKey();

        $order->load([
            'deliveryFeeDiscount',
            'orderDetails.orderDetailUnits.itemDetailIdentification',
            'orderDetails.displayedDiscount',
            'orderDetails.bundleSaleDiscount',
            'orderUsedCoupons.itemDiscount',
        ]);

        $portionedPoints = $this->orderPortion->portionPoints($order);
        $portionedCoupons = $this->orderPortion->portionCoupons($order);

        $ecDetails = $this->createEcDetails($order, $portionedPoints, $portionedCoupons);
        $ecBill = $this->createEcBill($order, $ecDetails);

        return $ecBill;
    }

    /**
     * @param OrderModel $order
     *
     * @return EcBill
     */
    private function createEcBill(OrderModel $order, \App\Entities\Collection $ecDetails)
    {
        $ecBill = new EcBill();
        $ecBill->code = $order->code;
        $ecBill->status = $order->status;
        $ecBill->delivery_date = optional($order->delivery_hope_date)->format('Y-m-d');
        $ecBill->total_price = $order->price;
        $ecBill->delivery_fee = $order->discounted_delivery_fee;
        $ecBill->delivery_fee_memo = \App\Enums\OrderDiscount\Type::getDescription($order->delivery_fee_discount_type);
        $ecBill->payment_fee = $order->fee;
        $ecBill->total_tax = $ecDetails->sum('tax')
            + Tax::calcTax($order->discounted_delivery_fee)
            + Tax::calcTax($order->fee);
        $ecBill->tax_excluded_total_price = $order->price - $ecBill->total_tax;
        $ecBill->detail_num = $ecDetails->count();
        $ecBill->details = $ecDetails;

        $paymentType = new EcPaymentType();
        $paymentType->id = $order->payment_type;
        $paymentType->name = \App\Enums\Order\PaymentType::getDescription($order->payment_type);
        $ecBill->payment = $paymentType;

        $orderAddresses = $order->orderAddresses;
        foreach ($orderAddresses as $orderAddress) {
            $address = new EcAddress();
            $address->type = $orderAddress->type;
            $address->lname = $orderAddress->lname;
            $address->fname = $orderAddress->fname;
            $address->lkana = $orderAddress->lkana;
            $address->fkana = $orderAddress->fkana;
            $address->tel = $orderAddress->tel;
            $address->pref_id = $orderAddress->pref_id;
            $address->zip = $orderAddress->zip;
            $address->city = $orderAddress->city;
            $address->town = $orderAddress->town;
            $address->address = $orderAddress->address;
            $address->building = $orderAddress->building;
            $address->email = $orderAddress->email;
            $type = strtolower(\App\Enums\OrderAddress\Type::fromValue($orderAddress->type)->key);
            $key = "{$type}_address";
            $ecBill->$key = $address;
        }

        return $ecBill;
    }

    /**
     * @param \App\Models\Order $order
     * @param SupportCollection $usedPoints
     * @param array $portionedCoupons
     *
     * @return \App\Entities\Collection
     */
    private function createEcDetails(OrderModel $order, SupportCollection $usedPoints, array $portionedCoupons)
    {
        $orderDetailUnits = $order->orderDetails->pluck('orderDetailUnits')->flatten();
        $ecDetails = EcDetail::collection();
        $usedPoints = Arr::dict($usedPoints, 'order_detail_unit_id');
        $orderDetailDict = Arr::dict($order->orderDetails);

        foreach ($orderDetailUnits as $unit) {
            $usedPoint = $usedPoints[$unit->id]['price'] ?? 0;
            $usedCoupons = $portionedCoupons[$unit->id] ?? collect();
            $orderDetail = $orderDetailDict[$unit->order_detail_id];

            $salePrice = ($orderDetail->price_before_order * $unit->amount)
                - $usedPoint
                - $usedCoupons->sum('price');
            $tax = Tax::calcTax($salePrice);

            $ecDetail = new EcDetail();
            $ecDetail->ec_id = $unit->id;
            $ecDetail->item_jan_code = $unit->itemDetailIdentification->jan_code;

            $ecDetail->retail_price = $orderDetail->retail_price;
            $ecDetail->sales_price = $salePrice;
            $ecDetail->amount = $unit->amount;
            $ecDetail->use_point = $usedPoint > 0 ? $usedPoint : null;

            $ecDetail->pb_div = $this->getPbDiv($orderDetail, $usedCoupons);
            $ecDetail->crosspoint_pb_div = $this->getCrosspointPbDiv($orderDetail);

            $ecDetail->tax_rate_id = $orderDetail->tax_rate_id;
            $ecDetail->tax_type = \App\Enums\Ymdy\Member\TaxType::Included;
            $ecDetail->tax = $tax;
            $ecDetail->tax_excluded_sale_price = $salePrice - $tax;
            $ecDetail->item_published_date = $orderDetail->itemDetail->item->sales_period_from;

            $ecColor = new EcColor();
            $ecColor->id = $orderDetail->itemDetail->color_id;
            $ecColor->code = $orderDetail->itemDetail->color->code;
            $ecColor->name = $orderDetail->itemDetail->color->name;
            $ecColor->display_name = $orderDetail->itemDetail->color->display_name;
            $ecDetail->color = $ecColor;

            $ecSize = new EcSize();
            $ecSize->id = $orderDetail->itemDetail->size_id;
            $ecSize->code = $orderDetail->itemDetail->size->code;
            $ecSize->search_code = $orderDetail->itemDetail->size->search_code;
            $ecSize->name = $orderDetail->itemDetail->size->name;
            $ecDetail->size = $ecSize;

            $ecDetail->discounts = EcDiscount::collection([]);

            foreach ($orderDetail->getOrderDiscounts() as $orderDiscount) {
                $discount = new EcDiscount();
                $discount->id = $orderDiscount->id;
                $discount->type = $this->convertEcDiscountTypeToMemberDiscountType($orderDiscount->type);
                $discount->price = $unit->amount > 0
                    ? $orderDiscount->unit_applied_price * $unit->amount
                    : 0;

                $ecDetail->discounts->add($discount);
            }

            foreach ($usedCoupons as $coupon) {
                $discount = new EcDiscount();
                $discount->id = $coupon['order_discount']->id;
                $discount->type = $this->convertEcDiscountTypeToMemberDiscountType($coupon['order_discount']->type);
                $discount->price = $coupon['price'];
                $discount->coupon_id = $coupon['coupon_id'];

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
    public static function convertEcDiscountTypeToMemberDiscountType($type)
    {
        switch ((int) $type) {
            case \App\Enums\OrderDiscount\Type::Normal:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::Normal;
            case \App\Enums\OrderDiscount\Type::Member:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::Member;
            case \App\Enums\OrderDiscount\Type::Staff:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::Staff;
            case \App\Enums\OrderDiscount\Type::EventSale:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::EventSale;
            case \App\Enums\OrderDiscount\Type::EventBundle:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::EventBundle;
            case \App\Enums\OrderDiscount\Type::CouponItem:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::Coupon;
            case \App\Enums\OrderDiscount\Type::Reservation:
                return \App\Enums\Ymdy\Member\Purchase\DiscountType::Reservation;

            default:
                throw new FatalException(error_format('error.invalid_argument_value', [__METHOD__, 'type' => $type]));
        }
    }

    /**
     * P/B区分の取得。区分はクーポンの按分結果に依存する。
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param SupportCollection $coupons
     *
     * @return int \App\Enums\Ymdy\Member\PvDiv
     */
    private static function getPbDiv(OrderDetail $orderDetail, SupportCollection $portionedCoupons)
    {
        if ($portionedCoupons->isNotEmpty()) {
            return \App\Enums\Ymdy\Member\PvDiv::Bargain;
        }

        return (int) $orderDetail->sale_type === \App\Enums\Order\SaleType::Employee
            ? \App\Enums\Ymdy\Member\PvDiv::Proper
            : \App\Enums\Ymdy\Member\PvDiv::Bargain;
    }

    /**
     * @param OrderDetail $orderDetail
     *
     * @return string \App\Enums\Ymdy\Member\CrosspointPvDiv
     */
    public static function getCrosspointPbDiv(OrderDetail $orderDetail)
    {
        if ($orderDetail->getOrderDiscounts()->isEmpty()) {
            return \App\Enums\Ymdy\Member\CrosspointPvDiv::Proper;
        }

        foreach ($orderDetail->getOrderDiscounts() as $orderDiscount) {
            switch ((int) $orderDiscount->type) {
                case \App\Enums\OrderDiscount\Type::Normal:
                case \App\Enums\OrderDiscount\Type::Member:
                case \App\Enums\OrderDiscount\Type::Staff:
                case \App\Enums\OrderDiscount\Type::EventSale:
                case \App\Enums\OrderDiscount\Type::EventBundle:
                case \App\Enums\OrderDiscount\Type::CouponItem:
                    return \App\Enums\Ymdy\Member\CrosspointPvDiv::Bargain;

                default:
                    break;
            }
        }

        return \App\Enums\Ymdy\Member\CrosspointPvDiv::Proper;
    }
}
