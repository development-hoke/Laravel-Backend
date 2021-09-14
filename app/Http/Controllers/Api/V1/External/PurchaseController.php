<?php

namespace App\Http\Controllers\Api\V1\External;

use App\Domain\PaymentInterface as PaymentService;
use App\Enums\Order\PaymentType;
use App\Exceptions\FatalException;
use App\Http\Controllers\Api\V1\External\Traits\ExclusiveLock;
use App\Http\Requests\Api\V1\External\Purchase\DeliveredRequest;
use App\Http\Resources\OrderDetail as OrderDetailResource;
use App\Http\Response;
use App\HttpCommunication\Ymdy\PurchaseInterface;
use App\Repositories\OrderRepository;
use App\Services\Admin\OrderDetailServiceInterface as OrderDetailService;
use App\Services\Front\PurchaseServiceInterface;
use App\Utils\Cache;
use App\Utils\OrderLog;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException as HttpException;

class PurchaseController extends Controller
{
    use ExclusiveLock;

    /**
     * @var array
     */
    private $baseRelations = [
        'itemDetail.item.itemImages',
        'itemDetail.color',
        'itemDetail.size',
        'itemDetail.item.department',
        'itemDetail.item.onlineCategories.root',
        'orderDetailUnits',
    ];

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderDetailService
     */
    private $orderDetailService;

    /**
     * @var PurchaseServiceInterface
     */
    protected $purchaseService;

    /**
     * @var PurchaseInterface
     */
    protected $purchaseHttp;

    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderDetailService $orderDetailService
     * @param PurchaseServiceInterface $purchaseService
     * @param PurchaseInterface $purchaseHttp
     * @param PaymentService $paymentService
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderDetailService $orderDetailService,
        PurchaseServiceInterface $purchaseService,
        PurchaseInterface $purchaseHttp,
        PaymentService $paymentService
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderDetailService = $orderDetailService;
        $this->purchaseService = $purchaseService;
        $this->purchaseHttp = $purchaseHttp;
        $this->paymentService = $paymentService;
    }

    /**
     * 配送処理
     *
     * @param DeliveredRequest $request
     * @param string $purchaseId
     *
     * @return array
     */
    public function delivered(DeliveredRequest $request, string $purchaseId)
    {
        if ($this->locked($purchaseId)) {
            // TODO: YMDYのエラーレスポンスに形状を合わせる
            throw new HttpException(Response::HTTP_CONFLICT);
        }

        try {
            $this->lock($purchaseId);

            $params = $request->validated();

            $order = $this->orderRepository->findOrFail([
                'code' => $purchaseId,
            ]);

            if (!$this->validateOrder($order)) {
                // TODO: YMDYのエラーレスポンスに形状を合わせる
                throw new HttpException(Response::HTTP_NOT_FOUND);
            }

            \App\Utils\OrderLog::purchased($order, __FUNCTION__, [
                'order' => $order->toArray(),
                'params' => $params,
            ]);

            try {
                DB::beginTransaction();

                // 配送完了処理
                $order = $this->orderRepository->changeToDelivered($order->id, $params);

                $this->capture($order);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                throw $e;
            }

            // 会員ポイントシステムの配送完了API(GET /api/v1/purchase/{purchase_id}/finish)実行
            OrderLog::purchased($order, 'finishPurchase', [
                'code' => $order->code,
            ]);
            try {
                $this->purchaseHttp->finishPurchase($order->code);
            } catch (\App\HttpCommunication\Exceptions\HttpException $e) {
                OrderLog::purchased($order, 'MemberApiError', [
                    'code' => $order->code,
                    'error' => $e->getMessage(),
                ]);
                $this->sendFailure([
                    'code' => $purchaseId,
                    'error' => $e->getMessage(),
                ]);
            }

            if ($order->deliveryed) {
                // 配送完了の通知メール
                OrderLog::purchased($order, 'sendDeliveredMail');

                $orderDetails = $this->orderDetailService->findByOrderId($order->id);

                $orderDetails->load($this->baseRelations);

                $data = [
                    'order' => $order,
                    'orderDetails' => OrderDetailResource::collection($orderDetails),
                ];
                $this->purchaseService->sendDeliveredMail($data);
            }

            return $this->success();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->unlock($purchaseId);
        }
    }

    /**
     * @param string $purchaseId
     *
     * @return string
     */
    protected function getExclusiveLockKey($purchaseId = null): string
    {
        return sprintf(Cache::KEY_ORDER_DELIVERED_LOCK, $purchaseId);
    }

    /**
     * 注文データが適切な状態か判定する
     *
     * @param \App\Models\Order $order
     *
     * @return bool
     */
    private function validateOrder(\App\Models\Order $order)
    {
        switch ($order->status) {
            case \App\Enums\Order\Status::Canceled:
            case \App\Enums\Order\Status::Returned:
            case \App\Enums\Order\Status::Deliveryed:
                return false;
        }

        return true;
    }

    /**
     * 支払い方法に応じて外部サービスに売上確保処理を実行する
     *
     * @param \App\Models\Order $order
     *
     * @return void
     */
    private function capture(\App\Models\Order $order)
    {
        try {
            $results = $this->paymentService->sale($order);

            // Amazon Payはオーソリが複数作成される可能性があるので、
            // 可能な限りキャプチャをして、エラーが発生した場合も例外を投げないため、
            // ここでAmazon Payのみ結果の検証と異常系の処理をする。
            if ($order->payment_type === PaymentType::AmazonPay) {
                $this->handleAmazonPayResults($results);
            }

            return;
        } catch (\Exception $e) {
            $this->sendFailure([
                'message' => 'キャプチャ処理に失敗しました。',
                'order_id' => $order->id,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Amazon Payのキャプチャ結果の異常系処理をする
     *
     * @param array $results
     *
     * @return void
     */
    private function handleAmazonPayResults(array $results)
    {
        if (empty($results['failed_reports'])) {
            return;
        }

        $messages = [];

        foreach ($results['failed_reports'] as $report) {
            $authorization = $report['authorization'];
            $exception = $report['exception'];

            $message = [
                'message' => 'キャプチャ処理に失敗しました。',
                'amazon_pay_authorization_id' => $authorization->id,
                'error_message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ];

            $this->sendFailure($message);

            $messages[] = implode("\n", $message);
        }

        throw new FatalException(implode("\n", $messages));
    }
}
