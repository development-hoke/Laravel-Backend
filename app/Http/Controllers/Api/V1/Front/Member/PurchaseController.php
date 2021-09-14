<?php

namespace App\Http\Controllers\Api\V1\Front\Member;

use App\Domain\OrderInterface as OrderService;
use App\Domain\Utils\OrderCancel;
use App\Exceptions\InvalidInputException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Member\Purchase\CancelRequest;
use App\Http\Requests\Api\V1\Front\Member\Purchase\IndexRequest;
use App\Http\Resources\Front\Order as OrderResource;
use App\Http\Response;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PurchaseController extends Controller
{
    /** @var OrderRepository */
    protected $orderRepository;

    /** @var OrderService */
    protected $orderService;

    public function __construct(
        OrderRepository $orderRepository,
        OrderService $orderService
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;

        if (auth('api')->check()) {
            $token = auth('api')->user()->token;
            $this->orderService->setMemberToken($token);
        }
    }

    /**
     * 購入一覧客
     *
     * @param $memberId
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    private function responseOrders($memberId)
    {
        $orders = $this->orderRepository->findWhere([
            'member_id' => $memberId,
            'deleted_at' => null,
        ]);
        if ($orders->isEmpty()) {
            return response()->json([]);
        }
        $orders->load([
            'deliveryFeeDiscount',
            'orderDetails.displayedDiscount',
            'orderDetails.bundleSaleDiscount',
            'orderDetails.orderDetailUnits',
            'orderUsedCoupons',
        ]);

        return response()->json(OrderResource::collection($orders));
    }

    /**
     * 購入履歴取得
     *
     * @param IndexRequest $request
     * @param $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request, $memberId)
    {
        return $this->responseOrders($memberId);
    }

    /**
     * 購入キャンセル
     *
     * @param CancelRequest $request
     * @param $memberId
     * @param $orderCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(CancelRequest $request, $memberId, $orderCode)
    {
        $order = $this->orderRepository->findWhere([
            'member_id' => $memberId,
            'code' => $orderCode,
        ])->first();

        if (empty($order)) {
            throw new InvalidInputException(__('error.no_orders'));
        }

        // キャンセル期間を過ぎている場合はエラー
        if (!OrderCancel::canCancelDatetime($order->order_date)) {
            throw new InvalidInputException(__('error.exceed_cancel_time'));
        }

        // キャンセル済みの場合はエラー
        if (!OrderCancel::canCancelStatus($order->status)) {
            throw new InvalidInputException(__('error.already_canceled'));
        }

        try {
            DB::beginTransaction();

            $this->orderService->cancel($order);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, __('validation.failed_order_cancel'), $e);
        }

        return $this->responseOrders($memberId);
    }
}
