<?php

namespace App\Services\Admin;

use App\Domain\Adapters\Ymdy\MemberPurchaseInterface as MemberPurchaseAdapter;
use App\Domain\Adapters\Ymdy\Purchase as PurchaseAdapter;
use App\Domain\CouponInterface as CouponService;
use App\Domain\Exceptions\PaymentRefundPartiallyException;
use App\Domain\Exceptions\PaymentUpdateBillingAmountException;
use App\Domain\ItemOrderDiscountInterface as ItemOrderDiscountService;
use App\Domain\ItemPriceInterface as ItemPriceService;
use App\Domain\MemberInterface as MemberService;
use App\Domain\OrderChangeHistoryInterface as OrderChangeHistoryService;
use App\Domain\OrderInterface as DomainOrderService;
use App\Domain\PaymentInterface as PaymentService;
use App\Exceptions\InvalidInputException;
use App\Repositories\ItemDetailRepository;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderDetailService extends BaseOrderService implements OrderDetailServiceInterface
{
    /**
     * @var OrderDetailRepository
     */
    private $orderDetailRepository;

    /**
     * @var ItemDetailRepository
     */
    private $itemDetailRepository;

    /**
     * @var ItemPriceService
     */
    private $itemPriceService;

    /**
     * @var CouponService
     */
    private $couponService;

    /**
     * @var DomainOrderService
     */
    private $domainOrderService;

    /**
     * @var ItemOrderDiscountService
     */
    private $itemOrderDiscountService;

    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * @var OrderChangeHistoryService
     */
    private $orderChangeHistoryService;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderDetailRepository $orderDetailRepository
     * @param ItemDetailRepository $itemDetailRepository
     * @param ItemPriceService $itemPriceService
     * @param MemberService $memberService
     * @param CouponService $couponService
     * @param PurchaseAdapter $purchaseAdapter
     * @param MemberPurchaseAdapter $memberPurchaseAdapter
     * @param DomainOrderService $domainOrderService
     * @param ItemOrderDiscountService $itemOrderDiscountService
     * @param PaymentService $paymentService
     * @param OrderChangeHistoryService $orderChangeHistoryService
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderDetailRepository $orderDetailRepository,
        ItemDetailRepository $itemDetailRepository,
        ItemPriceService $itemPriceService,
        MemberService $memberService,
        CouponService $couponService,
        PurchaseAdapter $purchaseAdapter,
        MemberPurchaseAdapter $memberPurchaseAdapter,
        DomainOrderService $domainOrderService,
        ItemOrderDiscountService $itemOrderDiscountService,
        PaymentService $paymentService,
        OrderChangeHistoryService $orderChangeHistoryService
    ) {
        parent::__construct($memberService, $purchaseAdapter, $memberPurchaseAdapter, $orderRepository);

        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->itemDetailRepository = $itemDetailRepository;
        $this->itemPriceService = $itemPriceService;
        $this->couponService = $couponService;
        $this->domainOrderService = $domainOrderService;
        $this->itemOrderDiscountService = $itemOrderDiscountService;
        $this->paymentService = $paymentService;
        $this->orderChangeHistoryService = $orderChangeHistoryService;

        if (auth('admin_api')->check()) {
            $token = auth('admin_api')->user()->token;
            $this->couponService->setStaffToken($token);
            $this->domainOrderService->setStaffToken($token);
        }
    }

    /**
     * @param int $orderId
     *
     * @return \App\Models\OrderDetail
     */
    public function findByOrderId(int $orderId)
    {
        $orderDetails = $this->orderDetailRepository->findWhere(['order_id' => $orderId]);

        $orderDetails->load(['displayedDiscount', 'bundleSaleDiscount']);

        return $orderDetails;
    }

    /**
     * @param int $id
     *
     * @return \App\Models\OrderDetail
     */
    public function findOne(int $id)
    {
        $orderDetail = $this->orderDetailRepository->find($id);

        return $orderDetail;
    }

    /**
     * @param array $params
     * @param int $orderId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function add(array $params, int $orderId)
    {
        try {
            DB::beginTransaction();

            $staffId = auth('admin_api')->id();

            $order = $this->orderRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->find($orderId);

            $member = $this->memberService->fetchOne($order->member_id);

            foreach ($params['items'] as $attributes) {
                $itemDetail = $this->itemDetailRepository->with([
                    'item',
                    'itemDetailIdentifications',
                ])->find($attributes['item_detail_id']);

                // 1. 割引価格の計算
                $this->itemPriceService->fillPriceBeforeOrderAfterOrdered($itemDetail->item, $order, $member, (int) $attributes['amount']);

                if ((int) $itemDetail->item->price_before_order !== (int) $attributes['price']) {
                    throw new InvalidInputException(__('validation.changed_price'));
                }

                // 2. order_detailsの作成
                $orderDetail = $this->orderDetailRepository->with(['displayedDiscount'])->findWhere([
                    'order_id' => $order->id,
                    'item_detail_id' => $attributes['item_detail_id'],
                ])->first();

                // 上代が異なるか商品割引価格（展示時点での価格）が異なる場合、同一商品詳細IDでも新しいレコードを作成する
                if ($isNewOrderDetail = $this->shouldCreateNewOrderDetail($itemDetail->item, $orderDetail)) {
                    $orderDetail = $this->domainOrderService->createOrderDetail($order, $itemDetail, $staffId);
                }

                // 3. 数量の更新
                $this->domainOrderService->addUnits($orderDetail, array_merge($attributes, [
                    'order_type' => $order->order_type,
                ]), ['staff_id' => $staffId]);

                // 4. 割引指定があった場合、商品割引の変更を反映する
                if ((int) $itemDetail->item->displayed_discount_type !== \App\Enums\Item\DiscountType::None) {
                    // 4-1. 受注詳細が新規作成の場合、order_discountsを新規作成する。
                    if ($isNewOrderDetail) {
                        $this->itemOrderDiscountService->createAndLoadDisplayedDiscount($order, $orderDetail, $itemDetail->item, $staffId);

                    // 4-2. 新規追加でない場合、増加した数量分の金額をapplied_priceに反映する必要があ
                    // NOTE: この時点で、受注詳細が既存でかつ割引適用の変更があるケースは存在しない。
                    } else {
                        $orderDetail = $this->itemOrderDiscountService->updateDisplayedDiscountAppliedPrice($orderDetail);
                    }
                }

                // 5. バンドル販売割引をを登録する
                // 数量と商品割引の結果、バンドル販売の価格に影響するため、数量と商品割引確定後に作成する
                $order = $this->itemOrderDiscountService->updateBundleSaleDiscount($order, $member, $staffId);

                // 6. クーポンの更新
                $order = $this->couponService->updateOrderRelatedCouponState($order, $staffId);

                // 7. sale_typeの更新
                // 注文商品と数量が変わることで割引の適用状態が変わり、sale_typeは影響を受けるため
                // 最後に注文中の全商品のsale_typeを設定し直す。
                $order = $this->domainOrderService->updateOrderDetailSaleTypes($order, $staffId);

                // 8. 最終的な合計金額 (orders.price) を再計算する
                $order = $this->domainOrderService->updateTotalPrice($order, $staffId);

                // 9. 付与ポイントの更新
                $order = $this->domainOrderService->updateAddPoint($order, $staffId);
            }

            $order = $this->orderRepository->with('orderDetails')->find($orderId);

            // 10. 外部サービスの更新
            // NOTE: 外部のみ更新されないように、DB更新の後に行う。
            // また、決済系のシステムのほうがエラーが起こりやすそうなので先に実行する

            // 10-1. 決済システムとの連携
            $this->paymentService->updateBillingAmount($order);

            // 10-2. 会員ポイントサービスの更新
            $ecBill = $this->purchaseAdapter->makeEcBill($order);
            $this->memberPurchaseAdapter->updateMemberPurchaseAndUpdateTax($order, $ecBill);

            DB::commit();

            return $order->orderDetails;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof \App\Domain\Exceptions\StockShortageException) {
                throw new InvalidInputException(__('error.not_exists_enouch_stock'), null, $e);
            }

            if ($e instanceof PaymentUpdateBillingAmountException) {
                $this->handleUpdateBillingAmountError($e);
            }

            throw $e;
        }
    }

    /**
     * @param int $orderId
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function cancel(int $orderId, array $params)
    {
        try {
            DB::beginTransaction();

            // データ取得。ordersに対して排他ロックを掛ける。
            // 計算結果がずれる可能性があるので、計算結果に依存するDBデータは確実のこれ以降の処理で参照する。（Repeatable Read対策）
            $order = $this->orderRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->find($orderId);

            $order->load(['orderDetails' => function ($query) use ($params) {
                return $query->whereIn('id', $params['ids']);
            }]);

            $order->orderDetails->load([
                'displayedDiscount',
                'bundleSaleDiscount',
                'orderDetailUnits',
            ]);

            $member = $this->memberService->fetchOne($order->member_id);
            $staffId = auth('admin_api')->id();

            foreach ($order->orderDetails as $orderDetail) {
                $originalAmount = $orderDetail->amount;
                $originalItemPrice = $orderDetail->price_before_order;

                $orderDetail = $this->domainOrderService->removeUnits($orderDetail, $staffId);

                $order = $this->itemOrderDiscountService->updateBundleSaleDiscount($order, $member, $staffId);

                $order = $this->couponService->updateOrderRelatedCouponState($order, $staffId);
                // 合計金額の更新
                $order = $this->domainOrderService->updateTotalPrice($order, $staffId);
                // 付与ポイントの更新
                $order = $this->domainOrderService->updateAddPoint($order, $staffId);

                $this->orderChangeHistoryService->createRemoveItemHistory($order, [
                    'amount' => $originalAmount,
                    'price' => -$originalItemPrice,
                    'item_detail_id' => $orderDetail->item_detail_id,
                ], $staffId);
            }

            // ここから外部サービスの更新
            // NOTE: 外部のみ更新されないように、DB更新の後に行う。
            // また、決済系のシステムのほうがエラーが起こりやすそうなので先に実行する

            // 決済システムとの連携
            $this->paymentService->updateBillingAmount($order);

            // 会員ポイントサービスの更新
            $ecBill = $this->purchaseAdapter->makeEcBill($order);
            $this->memberPurchaseAdapter->updateMemberPurchaseAndUpdateTax($order, $ecBill);

            $order = $this->orderRepository->with('orderDetails')->find($orderId);

            DB::commit();

            return $order->orderDetails;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof PaymentUpdateBillingAmountException) {
                $this->handleUpdateBillingAmountError($e);
            }

            throw $e;
        }
    }

    /**
     * @param \App\Models\Item $item
     * @param \App\Models\OrderDetail|null $orderDetail
     *
     * @return bool
     */
    private function shouldCreateNewOrderDetail(\App\Models\Item $item, \App\Models\OrderDetail $orderDetail = null)
    {
        if (empty($orderDetail)) {
            return true;
        }

        if ((int) $item->retail_price !== (int) $orderDetail->retail_price) {
            return true;
        }

        // バンドル販売以降は再計算されるので、ここでは、商品値引（商品展示に適用されていた値引）までを比較対象とする。
        if ((int) $item->displayed_sale_price !== (int) $orderDetail->displayed_sale_price) {
            return true;
        }

        if ($item->displayed_discount_type === \App\Enums\Item\DiscountType::None && empty($orderDetail->displayedDiscount)) {
            return false;
        }

        $discountTypeFromNew = \App\Domain\Utils\OrderDiscount::convertItemDiscoutToOrderDiscount($item->displayed_discount_type);
        $discountTypeFromExistent = empty($orderDetail->displayedDiscount) ? null : (int) $orderDetail->displayedDiscount->type;

        return $discountTypeFromNew !== $discountTypeFromExistent;
    }

    /**
     * 商品返品
     *
     * @param int $orderId
     * @param array $params
     *
     * @return \App\Models\OrderDetail
     */
    public function return(int $orderId, array $params)
    {
        try {
            DB::beginTransaction();

            // データ取得。ordersに対して排他ロックを掛ける。
            // 計算結果がずれる可能性があるので、計算結果に依存するDBデータは確実のこれ以降の処理で参照する。（Repeatable Read対策）
            $order = $this->orderRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->find($orderId);

            $order->load(['orderDetails' => function ($query) use ($params) {
                return $query->whereIn('id', $params['ids']);
            }]);

            $order->orderDetails->load([
                'displayedDiscount',
                'bundleSaleDiscount',
                'orderDetailUnits',
            ]);

            $member = $this->memberService->fetchOne($order->member_id);
            $staffId = auth('admin_api')->id();

            foreach ($order->orderDetails as $orderDetail) {
                $refundDetail = $this->domainOrderService->returnItem($orderDetail->id, $staffId);

                $order = $this->itemOrderDiscountService->updateBundleSaleDiscount($order, $member, $staffId);

                $order = $this->couponService->updateOrderRelatedCouponState($order, $staffId);

                $order = $this->domainOrderService->updateTotalPrice($order, $staffId);

                $this->orderChangeHistoryService->createReturnItemHistory($order, [
                    'amount' => $refundDetail->amount,
                    'price' => -$refundDetail->unit_price,
                    'item_detail_id' => $orderDetail->item_detail_id,
                ], $staffId);
            }

            // ここから外部サービスの更新
            // NOTE: 外部のみ更新されないように、DB更新の後に行う。
            // また、決済系のシステムのほうがエラーが起こりやすそうなので先に実行する
            $this->paymentService->refundPartially($order, $refundDetail->price);

            $ecBill = $this->purchaseAdapter->makeEcBill($order);

            $this->memberPurchaseAdapter->returnPartially($order->code, $ecBill);

            $order = $this->orderRepository->with('orderDetails')->find($orderId);

            DB::commit();

            return $order->orderDetails;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof PaymentRefundPartiallyException) {
                $this->handleRefundPartiallyError($e);
            }

            throw $e;
        }
    }
}
