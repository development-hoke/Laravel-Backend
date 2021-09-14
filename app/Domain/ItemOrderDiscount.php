<?php

namespace App\Domain;

use App\Domain\ItemPriceInterface as ItemPriceService;
use App\Domain\OrderChangeHistoryInterface as OrderChangeHistoryService;
use App\Domain\Utils\ItemPrice as ItemPriceUtil;
use App\Domain\Utils\OrderPrice;
use App\Repositories\OrderDiscountRepository;
use App\Repositories\OrderRepository;

/**
 * 商品関連の割引の処理を担当するクラス
 */
class ItemOrderDiscount implements ItemOrderDiscountInterface
{
    /**
     * @var OrderDiscountRepository
     */
    private $orderDiscountRepository;

    /**
     * @var ItemPriceService
     */
    private $itemPriceService;

    /**
     * @var OrderChangeHistoryService
     */
    private $orderChangeHistoryService;

    /**
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        OrderDiscountRepository $orderDiscountRepository,
        ItemPriceService $itemPriceService,
        OrderChangeHistoryService $orderChangeHistoryService
    ) {
        $this->orderDiscountRepository = $orderDiscountRepository;
        $this->itemPriceService = $itemPriceService;
        $this->orderChangeHistoryService = $orderChangeHistoryService;
    }

    /**
     * 受注に対して割引情報を作成してorderに読み込む。 (新規注文用)
     *
     * @param \App\Models\Order $order
     *
     * @return void
     */
    public function createAndLoadItemOrderDiscounts(\App\Models\Order $order)
    {
        foreach ($order->orderDetails as $orderDetail) {
            $item = $orderDetail->itemDetail->item;

            // 展示時の割引
            $this->createAndLoadDisplayedDiscount($order, $orderDetail, $item);

            // バンドル販売
            if (!empty($item->appliedBundleSale)) {
                $bundleSaleDiscount = $this->createBundleSaleDiscount($orderDetail, $item);
                $orderDetail->setRelation('bundleSaleDiscount', $bundleSaleDiscount);
            }
        }

        return $order;
    }

    /**
     * 展示時の割引を作成して読み込む
     *
     * @param \App\Models\Order $order
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\Item $item
     * @param int|null $staffId
     *
     * @return array
     */
    public function createAndLoadDisplayedDiscount(
        \App\Models\Order $order,
        \App\Models\OrderDetail $orderDetail,
        \App\Models\Item $item,
        ?int $staffId = null
    ) {
        // 展示時の商品割引
        if ((int) $item->displayed_discount_type !== \App\Enums\Item\DiscountType::None) {
            $displayedDiscount = $this->createDisplayedOrderDiscount($orderDetail, $item, $staffId);
            $orderDetail->setRelation('displayedDiscount', $displayedDiscount);
        }

        // 予約販売送料無料
        if ((int) $item->displayed_discount_type === \App\Enums\Item\DiscountType::Reservation && $item->is_free_delivery) {
            $reserationFreeDelivery = $this->createReservationFreeDerivery($order, $item, $staffId);
            $order->setRelation('deliveryFeeDiscount', $reserationFreeDelivery);
        }

        return [
            'order' => $order,
            'orderDetail' => $orderDetail,
        ];
    }

    /**
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\Item $requestedItem
     * @param int|null $staffId
     *
     * @return \App\Models\OrderDiscount
     */
    public function createDisplayedOrderDiscount(\App\Models\OrderDetail $orderDetail, \App\Models\Item $requestedItem, ?int $staffId = null)
    {
        $unitAppliedPrice = ItemPriceUtil::calcDisplayedDiscountPrice($requestedItem);

        $attributes = [];
        $attributes['orderable_type'] = \App\Models\OrderDetail::class;
        $attributes['orderable_id'] = $orderDetail->id;
        $attributes['unit_applied_price'] = $unitAppliedPrice;
        $attributes['applied_price'] = $unitAppliedPrice * $orderDetail->amount;
        $attributes['type'] = \App\Domain\Utils\OrderDiscount::convertItemDiscoutToOrderDiscount($requestedItem->displayed_discount_type);

        if (ItemPriceUtil::isPercentileMethodDisplayedDiscountType($requestedItem->displayed_discount_type)) {
            $attributes['method'] = \App\Enums\OrderDiscount\Method::Percentile;
            $attributes['discount_rate'] = $requestedItem->displayed_discount_rate;
        } else {
            $attributes['method'] = \App\Enums\OrderDiscount\Method::Fixed;
            $attributes['discount_price'] = $requestedItem->displayed_discount_price;
        }

        if (isset($staffId)) {
            $attributes['update_staff_id'] = $staffId;
        }

        switch ((int) $requestedItem->displayed_discount_type) {
            case \App\Enums\Item\DiscountType::Event:
                $attributes['discountable_type'] = get_class($requestedItem->applicableEvent);
                $attributes['discountable_id'] = $requestedItem->applicableEvent->id;

                break;
            case \App\Enums\Item\DiscountType::Reservation:
                $attributes['discountable_type'] = get_class($requestedItem->appliedReservation);
                $attributes['discountable_id'] = $requestedItem->appliedReservation->id;

                break;
            default:
                break;
        }

        $displayedDiscount = $this->orderDiscountRepository->create($attributes);

        return $displayedDiscount;
    }

    /**
     * 予約販売送料無料割引
     *
     * @param \App\Models\Order $order
     * @param \App\Models\Item $item
     * @param int|null $staffId
     *
     * @return \App\Models\OrderDiscount
     */
    public function createReservationFreeDerivery(\App\Models\Order $order, \App\Models\Item $item, ?int $staffId = null)
    {
        $orderDiscount = $this->orderDiscountRepository->makeModel();

        $orderDiscount->orderable_type = get_class($order);
        $orderDiscount->orderable_id = $order->id;
        $orderDiscount->applied_price = $order->delivery_fee;
        $orderDiscount->type = \App\Enums\OrderDiscount\Type::ReservationDeliveryFee;
        $orderDiscount->method = \App\Enums\OrderDiscount\Method::Fixed;
        $orderDiscount->discountable_type = get_class($item->appliedReservation);
        $orderDiscount->discountable_id = $item->appliedReservation->id;
        $orderDiscount->discount_price = $order->delivery_fee;

        if (isset($staffId)) {
            $orderDiscount->update_staff_id = $staffId;
        }

        $orderDiscount->save();

        return $orderDiscount;
    }

    /**
     * バンドル販売タイプのorder_discountsを作成
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\Item $item
     * @param int|null $staffId
     *
     * @return \App\Models\OrderDiscount
     */
    public function createBundleSaleDiscount(\App\Models\OrderDetail $orderDetail, \App\Models\Item $item, ?int $staffId = null)
    {
        $unitAppliedPrice = OrderPrice::calcDiscountingPriceByScalar(
            $orderDetail->displayed_sale_price,
            $item->bundle_discount_rate
        );

        $bundleSaleDiscount = $this->orderDiscountRepository->create([
            'orderable_type' => \App\Models\OrderDetail::class,
            'orderable_id' => $orderDetail->id,
            'unit_applied_price' => $unitAppliedPrice,
            'applied_price' => $unitAppliedPrice * $orderDetail->amount,
            'type' => \App\Enums\OrderDiscount\Type::EventBundle,
            'method' => \App\Enums\OrderDiscount\Method::Percentile,
            'discount_rate' => $item->bundle_discount_rate,
            'discountable_type' => get_class($item->appliedBundleSale),
            'discountable_id' => $item->appliedBundleSale->id,
            'update_staff_id' => $staffId,
        ]);

        return $bundleSaleDiscount;
    }

    /**
     * applied_priceの更新。数量に影響を受けるため、個別のメソッドを設ける。
     *
     * @param \App\Models\OrderDetail $orderDetail
     *
     * @return \App\Models\OrderDetail
     */
    public function updateDisplayedDiscountAppliedPrice(\App\Models\OrderDetail $orderDetail)
    {
        $orderDetail->load(['orderDetailUnits', 'displayedDiscount']);

        $salePrice = \App\Domain\Utils\OrderPrice::computeDisplayedSalePrice($orderDetail);

        $appliedPrice = ($orderDetail->retail_price - $salePrice) * $orderDetail->amount;

        $displayedDiscount = $orderDetail->displayedDiscount;

        $displayedDiscount->applied_price = $appliedPrice;

        $displayedDiscount->save();

        return $orderDetail;
    }

    /**
     * バンドル販売割引の更新
     * 対象商品の個数に影響を受けるため、まとめて更新する。
     *
     * @param \App\Models\Order $order
     * @param array $member
     * @param int $staffId
     *
     * @return \App\Models\Order
     */
    public function updateBundleSaleDiscount(\App\Models\Order $order, array $member, $staffId)
    {
        $order->load([
            'orderDetails.orderDetailUnits',
            'orderDetails.itemDetail.item',
            'orderDetails.displayedDiscount',
            'orderDetails.bundleSaleDiscount',
        ]);

        $items = $order->orderDetails->map(function ($orderDetail) {
            return $orderDetail->itemDetail->item;
        });

        $this->itemPriceService->fillPriceBeforeOrderAfterOrdered($items, $order, $member, 0);

        $order->orderDetails->each(function ($orderDetail) use ($staffId) {
            $item = $orderDetail->itemDetail->item;
            $bundleSaleDiscount = $orderDetail->bundleSaleDiscount;

            // Case 1. 更新後バンドル販売が適用されなかった場合
            if (empty($item->appliedBundleSale) && !empty($bundleSaleDiscount)) {
                $bundleSaleDiscount->softDeleteBy($staffId);
                $orderDetail->unsetRelation('bundleSaleDiscount');

            // Case 2. 既存のバンドル販売があり、再計算後もバンドル販売が適用された場合
            } elseif (!empty($item->appliedBundleSale) && !empty($bundleSaleDiscount)) {
                if ((float) $bundleSaleDiscount->discount_rate === (float) $item->bundle_discount_rate) {
                    return;
                }

                $originalUnitAppliedPrice = $bundleSaleDiscount->unit_applied_price;
                $bundleSaleDiscount->discount_rate = $item->bundle_discount_rate;
                $bundleSaleDiscount->unit_applied_price = OrderPrice::calcDiscountingPriceByScalar(
                    $orderDetail->displayed_sale_price,
                    $item->bundle_discount_rate
                );
                $bundleSaleDiscount->applied_price = $bundleSaleDiscount->unit_applied_price * $orderDetail->amount;
                $bundleSaleDiscount->discountable_id = $item->appliedBundleSale->id;
                $bundleSaleDiscount->update_staff_id = $staffId;
                $bundleSaleDiscount->save();

                // 生じた差額を履歴として保存する
                $this->orderChangeHistoryService->createRecalculateBundleSaleHistory(
                    $orderDetail,
                    $bundleSaleDiscount,
                    $originalUnitAppliedPrice,
                    $orderDetail->amount,
                    $staffId
                );

            // Case 3. 既存のバンドル販売なく、再計算後にバンドル販売が適用された場合
            } elseif (!empty($item->appliedBundleSale)) {
                $bundleSaleDiscount = $this->createBundleSaleDiscount($orderDetail, $item, $staffId);
                $orderDetail->setRelation('bundleSaleDiscount', $bundleSaleDiscount);

                // 生じた差額を履歴として保存する
                $this->orderChangeHistoryService->createRecalculateBundleSaleHistory(
                    $orderDetail,
                    $bundleSaleDiscount,
                    0,
                    $orderDetail->amount - 1,
                    $staffId
                );
            }
        });

        return $order;
    }
}
