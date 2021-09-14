<?php

namespace App\Domain;

use App\Models\Order as OrderModel;
use App\Utils\Arr;
use Illuminate\Database\Eloquent\Collection;

class OrderPortion implements OrderPortionInterface
{
    /**
     * ポイント按分計算
     *
     * @param \App\Models\Order $order
     * @param bool|null $loadRelation
     *
     * @return \Illuminate\Support\Collection
     */
    public function portionPoints(OrderModel $order, ?bool $loadRelation = true)
    {
        $order = $order->replicateWithKey();

        if ((int) $order->use_point === 0) {
            return collect([]);
        }

        return $this->portion($order->orderDetails, $order->use_point, $loadRelation);
    }

    /**
     * クーポン按分計算
     *
     * @param \App\Models\Order $order
     *
     * @return array
     */
    public function portionCoupons(OrderModel $order)
    {
        $order = $order->replicateWithKey();

        $portioned = [];

        if ($order->orderUsedCoupons->isEmpty()) {
            return $portioned;
        }

        foreach ($order->orderUsedCoupons as $orderUsedCoupon) {
            if ($orderUsedCoupon->item_applied_price === 0) {
                continue;
            }

            $targetOrderDetails = $this->getPortioningCouponTargetOrderDetails(
                $order->orderDetails,
                $orderUsedCoupon
            );

            $portion = $this->portion($targetOrderDetails, $orderUsedCoupon->item_applied_price, true);

            foreach ($portion as $data) {
                if (!isset($portioned[$data['order_detail_unit_id']])) {
                    $portioned[$data['order_detail_unit_id']] = collect([]);
                }

                $portioned[$data['order_detail_unit_id']]->add(array_merge($data, [
                    'coupon_id' => $orderUsedCoupon->coupon_id,
                    'order_discount' => $orderUsedCoupon->itemDiscount,
                ]));
            }
        }

        return $portioned;
    }

    /**
     * @param Collection $orderDetails
     * @param \App\Models\OrderUsedCoupon $orderUsedCoupon
     *
     * @return Collection
     */
    private function getPortioningCouponTargetOrderDetails(
        Collection $orderDetails,
        \App\Models\OrderUsedCoupon $orderUsedCoupon
    ) {
        $itemDiscount = $orderUsedCoupon->itemDiscount;

        if (empty($itemDiscount)) {
            return [];
        }

        if ($itemDiscount->method === \App\Enums\OrderDiscount\Method::Fixed) {
            return $orderDetails;
        }

        return $orderDetails->whereIn('id', $orderUsedCoupon->target_order_detail_ids);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $orderDetails
     * @param int $discountPrice
     * @param bool|null $skipLoadRelation
     *
     * @return \Illuminate\Support\Collection
     */
    private static function portion(
        \Illuminate\Database\Eloquent\Collection $orderDetails,
        int $discountPrice,
        ?bool $skipLoadRelation = false
    ) {
        !$skipLoadRelation && $orderDetails->load([
            'orderDetailUnits.itemDetailIdentification',
            'displayedDiscount',
            'bundleSaleDiscount',
        ]);

        $orderDetails = $orderDetails->filter(function ($orderDetail) {
            return $orderDetail->amount > 0;
        });

        $orderDetailUnits = $orderDetails->pluck('orderDetailUnits')->flatten();
        $orderDetailDict = Arr::dict($orderDetails);

        $totalPrice = $orderDetails->sum('total_price_before_order');
        $lastIndex = $orderDetailUnits->count() - 1;
        $proportion = collect([]);

        foreach (array_values($orderDetailUnits->all()) as $i => $unit) {
            if ($lastIndex > $i) {
                $orderDetail = $orderDetailDict[$unit->order_detail_id];
                $price = (int) floor((($orderDetail->price_before_order * $unit->amount) / $totalPrice) * $discountPrice);
            } else {
                $price = (int) ($discountPrice - $proportion->sum('price'));
            }

            $proportion->add([
                'price' => $price,
                'order_detail_unit_id' => $unit->id,
            ]);
        }

        return $proportion;
    }
}
