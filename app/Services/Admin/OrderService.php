<?php

namespace App\Services\Admin;

use App\Criteria\Order\AdminSearchCriteria;
use App\Domain\Adapters\Ymdy\MemberPurchaseInterface as MemberPurchaseAdapter;
use App\Domain\Adapters\Ymdy\Purchase as PurchaseAdapter;
use App\Domain\CouponInterface as CouponService;
use App\Domain\Exceptions\PaymentRefundException;
use App\Domain\Exceptions\PaymentUpdateBillingAmountException;
use App\Domain\Exceptions\PaymentUpdateShippingAddressException;
use App\Domain\MemberInterface as MemberService;
use App\Domain\OrderInterface as DomainOrderService;
use App\Domain\PaymentInterface as PaymentService;
use App\Exceptions\FailedAddingCouponException;
use App\Exceptions\InvalidInputException;
use App\Repositories\OrderAddressRepository;
use App\Repositories\OrderChangeHistoryRepository;
use App\Repositories\OrderMessageRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderUsedCouponRepository;
use App\Utils\Arr;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseOrderService implements OrderServiceInterface
{
    /**
     * @var OrderAddressRepository
     */
    private $orderAddressRepository;

    /**
     * @var OrderChangeHistoryRepository
     */
    private $orderChangeHistoryRepository;

    /**
     * @var CouponService
     */
    private $couponService;

    /**
     * @var DomainOrderService
     */
    private $domainOrderService;

    /**
     * @var OrderMessageRepository
     */
    private $orderMessageRepository;

    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * @var OrderUsedCouponRepository
     */
    private $orderUsedCouponRepository;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderAddressRepository $orderAddressRepository
     * @param OrderChangeHistoryRepository $orderChangeHistoryRepository
     * @param MemberService $memberService
     * @param CouponService $couponService
     * @param PurchaseAdapter $purchaseAdapter
     * @param MemberPurchaseAdapter $memberPurchaseAdapter
     * @param DomainOrderService $domainOrderService
     * @param OrderMessageRepository $orderMessageRepository
     * @param PaymentService $paymentService
     * @param OrderUsedCouponRepository $orderUsedCouponRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderAddressRepository $orderAddressRepository,
        OrderChangeHistoryRepository $orderChangeHistoryRepository,
        MemberService $memberService,
        CouponService $couponService,
        PurchaseAdapter $purchaseAdapter,
        MemberPurchaseAdapter $memberPurchaseAdapter,
        DomainOrderService $domainOrderService,
        OrderMessageRepository $orderMessageRepository,
        PaymentService $paymentService,
        OrderUsedCouponRepository $orderUsedCouponRepository
    ) {
        parent::__construct($memberService, $purchaseAdapter, $memberPurchaseAdapter, $orderRepository);

        $this->orderAddressRepository = $orderAddressRepository;
        $this->orderChangeHistoryRepository = $orderChangeHistoryRepository;
        $this->couponService = $couponService;
        $this->domainOrderService = $domainOrderService;
        $this->orderMessageRepository = $orderMessageRepository;
        $this->paymentService = $paymentService;
        $this->orderUsedCouponRepository = $orderUsedCouponRepository;

        if (auth('admin_api')->check()) {
            $token = auth('admin_api')->user()->token;
            $this->couponService->setStaffToken($token);
            $this->domainOrderService->setStaffToken($token);
        }
    }

    /**
     * 検索
     *
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(array $params)
    {
        $this->orderRepository->pushCriteria(new AdminSearchCriteria($params));

        $orders = $this->orderRepository->paginate(
            $params['per_page'] ?? config('repository.pagination.order', 50)
        );

        return $orders;
    }

    /**
     * 注文情報取得
     *
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function findOne(int $id)
    {
        $order = $this->orderRepository->find($id);

        $this->loadRelationForOrderDetail($order);

        $this->loadRelationForOrderDetailFromOutside($order);

        return $order;
    }

    /**
     * 更新
     * NOTE: この処理はステータスや配送情報の更新を行い、金額の計算は行わない。
     *
     * @param array $attributes
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function update(array $attributes, int $id)
    {
        try {
            DB::beginTransaction();

            $staffId = auth('admin_api')->id();
            $orderAttributes = Arr::except($attributes, ['delivery_address']);

            if (!empty($orderAttributes)) {
                $orderAttributes['update_staff_id'] = $staffId;
                $order = $this->orderRepository->update($orderAttributes, $id);
            }

            if (!empty($attributes['delivery_address'])) {
                $deliveryAddress = $this->orderAddressRepository->findDeliveryAddress($id);
                $attributes['delivery_address']['update_staff_id'] = $staffId;
                $this->orderAddressRepository->update($attributes['delivery_address'], $deliveryAddress->id);

                $order = $this->orderRepository->find($id);
                $this->paymentService->updateShippingAddress($order);
            }

            $order = $this->orderRepository->find($id);
            $this->loadRelationForOrderDetail($order);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof PaymentUpdateShippingAddressException) {
                $this->handleUpdateShippingAddressError($e);
            }

            throw $e;
        }

        $this->loadRelationForOrderDetailFromOutside($order);

        return $order;
    }

    /**
     * 受注キャンセル
     *
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function cancel(int $id)
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepository->find($id);

            $order = $this->domainOrderService->cancel($order, auth('admin_api')->id());

            $order = $this->orderRepository->find($order->id);

            $order = $this->loadRelationForOrderDetail($order);

            DB::commit();

            $order = $this->loadRelationForOrderDetailFromOutside($order);

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof \App\HttpCommunication\Exceptions\InvalidSystemException) {
                throw $e;
            }

            if ($e instanceof \App\HttpCommunication\Exceptions\HttpException) {
                $response = $e->getResponseBody();

                if (!empty($response['message'])) {
                    throw new InvalidInputException($response['message'], null, $e);
                }

                if ($e->getResponseStatusCode() < 500) {
                    throw new InvalidInputException($e->getMessage(), null, $e);
                }
            }

            throw $e;
        }
    }

    /**
     * @param \App\Models\Order $order
     *
     * @return \App\Models\Order
     */
    private function loadRelationForOrderDetail(\App\Models\Order $order)
    {
        $order->acceptable_payment_types = $this->getAcceptablePaymentTypes();

        $order->load([
            'orderChangeHistories.staff',
            'orderMessages',
            'memberOrderAddress.pref',
            'deliveryOrderAddress.pref',
            'billingOrderAddress.pref',
            'deliveryFeeDiscount',
        ]);

        $order->orderChangeHistories->loadItemDetail([
            'item.onlineCategories.root',
            'item.itemImages',
            'item.department',
            'color',
            'size',
        ]);

        return $order;
    }

    /**
     * @param \App\Models\Order $order
     *
     * @return \App\Models\Order
     */
    private function loadRelationForOrderDetailFromOutside(\App\Models\Order $order)
    {
        $this->couponService->loadUsedCouponsWithDetail($order);

        return $order;
    }

    /**
     * クーポン追加処理
     *
     * @param array $attributes
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function addCoupon(array $attributes, int $id)
    {
        try {
            DB::beginTransaction();

            // NOTE: 排他ロックを掛ける。
            // 編集が競合すると計算結果がずれるので、DBの参照は確実のこれ以降の処理で行う。
            $order = $this->orderRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->find($id);

            $order->load(['orderDetails.orderDetailUnits', 'orderDetails.itemDetail']);

            $coupon = $this->getValidatedCoupon($order, $attributes['coupon_id']);

            $staffId = auth('admin_api')->id();

            $orderUsedCoupon = $this->couponService->addCoupon($coupon, $order, $staffId);

            $order = $this->orderRepository->find($id);
            // 合計金額の更新
            $order = $this->domainOrderService->updateTotalPrice($order, $staffId);
            // 付与ポイントの更新
            $order = $this->domainOrderService->updateAddPoint($order, $staffId);

            $orderLog = $order->getLatestLog();

            $this->orderChangeHistoryRepository->create([
                'order_id' => $order->id,
                'log_type' => get_class($orderLog),
                'log_id' => $orderLog->id,
                'staff_id' => $staffId,
                'event_type' => \App\Enums\OrderChangeHistory\EventType::AddCoupon,
                'diff_json' => [
                    'coupon_id' => $coupon->id,
                    'name' => $coupon->name,
                    'price' => -$orderUsedCoupon->total_applied_price,
                ],
            ]);

            // ここから外部サービスとの連携
            // NOTE: 外部のみ更新されないように、DB更新の後に行う。
            // 決済系のシステムのほうがエラーが起こりやすそうなので先に実行する
            $this->paymentService->updateBillingAmount($order);

            $ecBill = $this->purchaseAdapter->makeEcBill($order);

            $this->memberPurchaseAdapter->updateMemberPurchaseAndUpdateTax($order, $ecBill);

            $order = $this->loadRelationForOrderDetail(
                $this->orderRepository->find($order->id)
            );

            DB::commit();

            $order = $this->loadRelationForOrderDetailFromOutside($order);

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof PaymentUpdateBillingAmountException) {
                $this->handleUpdateBillingAmountError($e);
            }

            if ($e instanceof FailedAddingCouponException) {
                throw new InvalidInputException(['coupon_id' => $e->getMessage()], null, $e);
            }

            throw $e;
        }
    }

    /**
     * @param \App\Models\Order $order
     * @param int $couponId
     *
     * @return \App\Entities\Ymdy\Member\Coupon
     *
     * @throws InvalidInputException
     */
    private function getValidatedCoupon(\App\Models\Order $order, $couponId)
    {
        $order->load(['orderUsedCoupons']);

        $coupon = $this->couponService->fetchCoupon($couponId);

        return $coupon;
    }

    /**
     * クーポ削除処理
     *
     * @param array $attributes
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function removeCoupon(array $attributes, int $id)
    {
        try {
            DB::beginTransaction();

            $staffId = auth('admin_api')->id();

            // NOTE: 排他ロックを掛ける。
            // 編集が競合すると計算結果がずれるので、DBの参照は確実のこれ以降の処理で行う。
            $order = $this->orderRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->find($id);

            $orderUsedCoupon = $this->orderUsedCouponRepository->with([
                'itemDiscount',
                'deliveryFeeDiscount',
            ])->find($attributes['order_used_coupon_id']);
            $originalPrice = $orderUsedCoupon->total_applied_price;

            $removedUsedCoupon = $this->couponService->removeCoupon($attributes['order_used_coupon_id']);
            $removedUsedCoupon = $this->couponService->loadCouponToOrderUsedCoupons($removedUsedCoupon);

            $order = $this->orderRepository->find($id);
            // 合計金額の更新
            $order = $this->domainOrderService->updateTotalPrice($order, $staffId);
            // 付与ポイントの更新
            $order = $this->domainOrderService->updateAddPoint($order, $staffId);

            $orderLog = $order->getLatestLog();

            $this->orderChangeHistoryRepository->create([
                'order_id' => $order->id,
                'log_type' => get_class($orderLog),
                'log_id' => $orderLog->id,
                'staff_id' => $staffId,
                'event_type' => \App\Enums\OrderChangeHistory\EventType::RemoveCoupon,
                'diff_json' => [
                    'order_used_coupon_id' => $attributes['order_used_coupon_id'],
                    'name' => $removedUsedCoupon->coupon->name,
                    'price' => $originalPrice,
                ],
            ]);

            // ここから外部サービスとの連携
            // NOTE: 外部のみ更新されないように、DB更新の後に行う。
            // 決済系のシステムのほうがエラーが起こりやすそうなので先に実行する
            $this->paymentService->updateBillingAmount($order);
            // 会員ポイントサービスの更新
            $ecBill = $this->purchaseAdapter->makeEcBill($order);

            $this->memberPurchaseAdapter->updateMemberPurchaseAndUpdateTax($order, $ecBill);

            $order = $this->loadRelationForOrderDetail(
                $this->orderRepository->find($order->id)
            );

            DB::commit();

            $order = $this->loadRelationForOrderDetailFromOutside($order);

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof PaymentUpdateBillingAmountException) {
                $this->handleUpdateBillingAmountError($e);
            }

            throw $e;
        }
    }

    /**
     * 金額の更新をする
     *
     * @param array $attributes
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function updatePrice(array $attributes, int $id)
    {
        try {
            DB::beginTransaction();

            $staffId = auth('admin_api')->id();

            // NOTE: 排他ロックを掛ける。
            // 編集が競合すると計算結果がずれるので、DBの参照は確実のこれ以降の処理で行う。
            $order = $this->orderRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->find($id);

            $order->changed_price += $attributes['price_diff'];
            // 合計金額の更新
            $order = $this->domainOrderService->updateTotalPrice($order, $staffId);
            // 付与ポイントの更新
            $order = $this->domainOrderService->updateAddPoint($order, $staffId);

            $orderLog = $order->getLatestLog();

            $this->orderChangeHistoryRepository->create([
                'order_id' => $order->id,
                'log_type' => get_class($orderLog),
                'log_id' => $orderLog->id,
                'staff_id' => $staffId,
                'event_type' => \App\Enums\OrderChangeHistory\EventType::ChangePrice,
                'diff_json' => ['price' => $attributes['price_diff']],
                'memo' => $attributes['log_memo'],
            ]);

            // ここから外部サービスとの連携
            // NOTE: 外部のみ更新されないように、DB更新の後に行う。
            // 決済系のシステムのほうがエラーが起こりやすそうなので先に実行する
            $this->paymentService->updateBillingAmount($order);
            // 会員ポイントサービスの更新
            $ecBill = $this->purchaseAdapter->makeEcBill($order);

            $this->memberPurchaseAdapter->updateMemberPurchaseAndUpdateTax($order, $ecBill);

            $order = $this->loadRelationForOrderDetail(
                $this->orderRepository->find($order->id)
            );

            DB::commit();

            $order = $this->loadRelationForOrderDetailFromOutside($order);

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof PaymentUpdateBillingAmountException) {
                $this->handleUpdateBillingAmountError($e);
            }

            throw $e;
        }
    }

    /**
     * 管理画面側で変更可能なpayment_type
     *
     * @return array
     */
    public static function getAcceptablePaymentTypes()
    {
        return [\App\Enums\Order\PaymentType::Cod];
    }

    /**
     * 購入者へのメッセージ送信
     *
     * @param int $orderId
     * @param array $params
     *
     * @return \App\Models\OrderMessage
     */
    public function sendOrderMessage(int $orderId, array $params)
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepository->find($orderId);

            $member = $this->memberService->fetchOne($order->member_id);

            $orderMessage = $this->orderMessageRepository->create([
                'order_id' => $orderId,
                'title' => $params['title'],
                'body' => nl2br($params['body']),
                'type' => \App\Enums\OrderMessage\Type::Store,
            ]);

            \App\Jobs\SendOrderMessage::dispatch($orderMessage, ['member' => $member]);

            DB::commit();

            return $orderMessage;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 注文返品
     *
     * @param int $orderId
     *
     * @return \App\Models\Order
     */
    public function return($orderId)
    {
        try {
            DB::beginTransaction();

            $staffId = auth('admin_api')->id();

            $order = $this->orderRepository->find($orderId);

            $this->domainOrderService->return($order->id, $staffId);

            // ここから外部サービスとの連携
            // NOTE: 外部のみ更新されないように、DB更新の後に行う。
            // 決済系のシステムのほうがエラーが起こりやすそうなので先に実行する
            $this->paymentService->refund($order);

            $this->memberPurchaseAdapter->returnOrder($order->code);

            $order = $this->loadRelationForOrderDetail(
                $this->orderRepository->find($orderId)
            );

            DB::commit();

            $order = $this->loadRelationForOrderDetailFromOutside($order);

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof PaymentRefundException) {
                $this->handleRefundError($e);
            }

            throw $e;
        }
    }
}
