<?php

namespace App\Domain;

use App\Domain\Adapters\AmazonPayAdapterInterface as AmazonPayAdapter;
use App\Domain\Exceptions\AmazonPayInsufficientRefundingCaptureException;
use App\Exceptions\FatalException;
use App\Exceptions\InvalidInputException;
use App\Repositories\AmazonPay\AuthorizationRepository;
use App\Repositories\AmazonPay\CaptureRepository;
use App\Repositories\AmazonPay\OrderRepository as AmazonPayOrderRepository;
use App\Repositories\AmazonPay\RefundRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class AmazonPay implements AmazonPayInterface
{
    /**
     * @var \App\Domain\Adapters\AmazonPayAdapterInterface
     */
    private $amazonPayAdapter;

    /**
     * @var AmazonPayOrderRepository
     */
    private $amazonPayOrderRepository;

    /**
     * @var AuthorizationRepository
     */
    private $authorizationRepository;

    /**
     * @var CaptureRepository
     */
    private $captureRepository;

    /**
     * @var RefundRepository
     */
    private $refundRepository;

    /**
     * @param AmazonPayAdapter $amazonPayAdapter
     * @param AmazonPayOrderRepository $amazonPayOrderRepository
     * @param AuthorizationRepository $authorizationRepository
     * @param CaptureRepository $captureRepository
     * @param RefundRepository $refundRepository
     */
    public function __construct(
        AmazonPayAdapter $amazonPayAdapter,
        AmazonPayOrderRepository $amazonPayOrderRepository,
        AuthorizationRepository $authorizationRepository,
        CaptureRepository $captureRepository,
        RefundRepository $refundRepository
    ) {
        $this->amazonPayAdapter = $amazonPayAdapter;
        $this->amazonPayOrderRepository = $amazonPayOrderRepository;
        $this->authorizationRepository = $authorizationRepository;
        $this->captureRepository = $captureRepository;
        $this->refundRepository = $refundRepository;
    }

    /**
     * @param string $orderReferenceId
     * @param string|null $accessToken
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function findOrderReferenceDetails(string $orderReferenceId, ?string $accessToken = null)
    {
        $options = $accessToken ? ['access_token' => $accessToken] : [];

        $orderReferenceDetails = $this->amazonPayAdapter->getOrderReferenceDetails($orderReferenceId, $options);

        return $orderReferenceDetails;
    }

    /**
     * 注文金額を設定して、Constraintsの有無を検証する
     *
     * @param string $orderReferenceId
     * @param int $totalAmount
     * @param string $accessToken
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function orderConfirm(string $orderReferenceId, int $totalAmount, string $accessToken)
    {
        $orderReferenceDetails = $this->amazonPayAdapter->setOrderReferenceDetails($orderReferenceId, $totalAmount);

        $this->validateConstraintsToOrder($orderReferenceDetails);

        // 情緒など詳細情報が取れるので個別でリクエストする。
        $orderReferenceDetails = $this->amazonPayAdapter->getOrderReferenceDetails($orderReferenceId, [
            'access_token' => $accessToken,
        ]);

        return $orderReferenceDetails;
    }

    /**
     * 注文処理の実行
     * (1) OrderReferenceDetailsの取得とConstraintsの確認
     * (2) amazon_pay_ordersテーブルにレコードを作成
     * (3) OrderReferenceをOpenにする
     * (4) オーソリの実行
     *
     * @param string $orderReferenceId
     * @param int $orderId
     * @param array $params ['amazon_access_token' => string]
     *
     * @return \App\Models\AmazonPayOrder
     */
    public function order(string $orderReferenceId, int $orderId, array $params)
    {
        $this->amazonPayAdapter->confirmOrderReference($orderReferenceId);

        $orderReferenceDetails = $this->amazonPayAdapter->getOrderReferenceDetails($orderReferenceId, [
            'access_token' => $params['amazon_access_token'],
        ]);

        $amazonPayOrder = $this->createAmazonPayOrder($orderReferenceDetails, $orderId);

        $this->authorize($orderReferenceId, $amazonPayOrder->id, $amazonPayOrder->amount);

        $amazonPayOrder->setRelation('orderReferenceDetails', $orderReferenceDetails);

        return $amazonPayOrder;
    }

    /**
     * オーソリを実行し、amazon_pay_authorizationsテーブルにレコードを作成する
     *
     * @param string $orderReferenceId
     * @param int $amazonPayOrderId
     * @param int $amount
     *
     * @return \App\Models\AmazonPayAuthorization
     */
    public function authorize(string $orderReferenceId, int $amazonPayOrderId, int $amount)
    {
        $authorizationReferenceId = $this->authorizationRepository->generateReferenceId();

        $authorizationDetails = $this->amazonPayAdapter->authorize($orderReferenceId, $authorizationReferenceId, $amount);

        if ($authorizationDetails->authorization_status->state === \App\Enums\AmazonPay\Status\Authorization::Declined) {
            $this->handleAuthorizationDeclined($authorizationDetails);
        }

        $amazonPayAuthorization = $this->createAuthorization($authorizationDetails, $amazonPayOrderId);

        return $amazonPayAuthorization;
    }

    private function handleAuthorizationDeclined(\App\Entities\AmazonPay\AuthorizationDetails $authorizationDetails)
    {
        switch ($authorizationDetails->authorization_status->reason_code) {
            case \App\Enums\AmazonPay\StatusReason\Authorization::InvalidPaymentMethod:
                throw new InvalidInputException($authorizationDetails->soft_decline
                    ? __('validation.amazon_pay.invalid_payment_method_soft_decline')
                    : __('validation.amazon_pay.invalid_payment_method_hard_decline'));

            case \App\Enums\AmazonPay\StatusReason\Authorization::AmazonRejected:
                throw new InvalidInputException(__('validation.amazon_pay.amazon_rejected'));
            case \App\Enums\AmazonPay\StatusReason\Authorization::ProcessingFailure:
                throw new InvalidInputException(__('validation.amazon_pay.processing_failure'));
            case \App\Enums\AmazonPay\StatusReason\Authorization::TransactionTimedOut:
                throw new InvalidInputException(__('validation.amazon_pay.transaction_timed_out'));
        }
    }

    /**
     * amazon_pay_ordersテーブルにレコードを作成する
     *
     * @param \App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails
     * @param int $orderId
     *
     * @return \App\Models\AmazonPayOrder
     */
    public function createAmazonPayOrder(\App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails, int $orderId)
    {
        return $this->amazonPayOrderRepository->create([
            'order_id' => $orderId,
            'order_reference_id' => $orderReferenceDetails->amazon_order_reference_id,
            'status' => $orderReferenceDetails->order_reference_status->state,
            'status_reason_code' => $orderReferenceDetails->order_reference_status->reason_code,
            'last_status_updated_at' => $orderReferenceDetails->order_reference_status->last_update_timestamp,
            'amount' => $orderReferenceDetails->order_total->amount,
            'expiration_at' => $orderReferenceDetails->expiration_timestamp,
        ]);
    }

    /**
     * amazon_pay_ordersを更新
     *
     * @param \App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails
     * @param int $orderId
     *
     * @return \App\Models\AmazonPayOrder
     */
    public function importAmazonPayOrder(\App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails)
    {
        $amazonPayOrder = $this->amazonPayOrderRepository->findWhere([
            'order_reference_id' => $orderReferenceDetails->amazon_order_reference_id,
        ])->first();

        if (empty($amazonPayOrder)) {
            throw (new ModelNotFoundException(error_format('error.model_not_found', [
                'order_reference_id' => $orderReferenceDetails->amazon_order_reference_id,
            ])))->setModel(\App\Models\AmazonPayOrder::class);
        }

        $amazonPayOrder = $this->amazonPayOrderRepository->update([
            'status' => $orderReferenceDetails->order_reference_status->state,
            'status_reason_code' => $orderReferenceDetails->order_reference_status->reason_code,
            'last_status_updated_at' => $orderReferenceDetails->order_reference_status->last_update_timestamp,
            'amount' => $orderReferenceDetails->order_total->amount,
            'expiration_at' => $orderReferenceDetails->expiration_timestamp,
        ], $amazonPayOrder->id);

        return $amazonPayOrder;
    }

    /**
     * amazon_pay_authorizationsテーブルにレコードを作成する
     *
     * @param \App\Entities\AmazonPay\AuthorizationDetails $authorizationDetails
     * @param int $amazonPayOrderId
     *
     * @return \App\Models\AmazonPayAuthorization
     */
    public function createAuthorization(
        \App\Entities\AmazonPay\AuthorizationDetails $authorizationDetails,
        int $amazonPayOrderId
    ) {
        return $this->authorizationRepository->create([
            'amazon_pay_order_id' => $amazonPayOrderId,
            'authorization_reference_id' => $authorizationDetails->authorization_reference_id,
            'amazon_authorization_id' => $authorizationDetails->amazon_authorization_id,
            'status' => $authorizationDetails->authorization_status->state,
            'status_reason_code' => $authorizationDetails->authorization_status->reason_code,
            'last_status_updated_at' => $authorizationDetails->authorization_status->last_update_timestamp,
            'soft_decline' => $authorizationDetails->soft_decline,
            'amount' => $authorizationDetails->authorization_amount->amount,
            'capturing_amount' => $authorizationDetails->authorization_amount->amount,
            'fee' => $authorizationDetails->authorization_fee->amount,
            'expiration_at' => $authorizationDetails->expiration_timestamp,
        ]);
    }

    /**
     * amazon_pay_authorizationの更新
     *
     * @param \App\Entities\AmazonPay\AuthorizationDetails $authorizationDetails
     * @param bool|null $delete
     *
     * @return \App\Models\AmazonPayAuthorization
     */
    public function importAuthorizationDetails(
        \App\Entities\AmazonPay\AuthorizationDetails $authorizationDetails,
        ?bool $delete = false
    ) {
        $authorization = $this->authorizationRepository->findWhere([
            'authorization_reference_id' => $authorizationDetails->authorization_reference_id,
            'amazon_authorization_id' => $authorizationDetails->amazon_authorization_id,
        ])->first();

        if (empty($authorization)) {
            throw (new ModelNotFoundException(error_format('error.model_not_found', [
                'authorization_reference_id' => $authorizationDetails->authorization_reference_id,
                'amazon_authorization_id' => $authorizationDetails->amazon_authorization_id,
            ])))->setModel(\App\Models\AmazonPayAuthorization::class);
        }

        $authorization = $this->authorizationRepository->update([
            'status' => $authorizationDetails->authorization_status->state,
            'status_reason_code' => $authorizationDetails->authorization_status->reason_code,
            'last_status_updated_at' => $authorizationDetails->authorization_status->last_update_timestamp,
            'soft_decline' => $authorizationDetails->soft_decline,
            'amount' => $authorizationDetails->authorization_amount->amount,
            'capturing_amount' => $authorizationDetails->authorization_amount->amount,
            'fee' => $authorizationDetails->authorization_fee->amount,
            'expiration_at' => $authorizationDetails->expiration_timestamp,
        ], $authorization->id);

        if ($delete) {
            $this->authorizationRepository->delete($authorization->id);
        }

        return $authorization;
    }

    /**
     * 注文実行時のConstraintsのバリデーション。
     * 一部はシステムエラーとして処理する。
     *
     * @param \App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails
     * @param \App\Models\Order $order
     *
     * @return void
     */
    private function validateConstraintsToOrder(\App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails)
    {
        $constraints = $orderReferenceDetails->constraints;

        if ($constraints->isEmpty()) {
            return;
        }

        foreach ($constraints as $constraint) {
            switch ($constraint->constraint_id) {
                // (1) ユーザー入力に対するエラーとしてクライアントサイドに返す
                case \App\Enums\AmazonPay\Constraint::BuyerEqualsSeller:
                    throw new InvalidInputException(__('validation.amazon_pay.constraint_buyer_equals_seller'));
                case \App\Enums\AmazonPay\Constraint::PaymentMethodNotAllowed:
                    throw new InvalidInputException(__('validation.amazon_pay.constraint_payment_method_not_allowed'));
                case \App\Enums\AmazonPay\Constraint::PaymentPlanNotSet:
                    throw new InvalidInputException(__('validation.amazon_pay.constraint_payment_plan_not_set'));
                case \App\Enums\AmazonPay\Constraint::ShippingAddressNotSet:
                    throw new InvalidInputException(__('validation.amazon_pay.constraint_shipping_address_not_set'));
                // (2) 以降のconstraint_idはシステムエラーとして返却する
                // 金額が設定されていない、またはECシステム側で認識していないConstraintが返却された場合、システムエラー扱いにする。
                case \App\Enums\AmazonPay\Constraint::AmountNotSet:
                    throw new FatalException(__('error.amazon_pay_order_amount_not_set', [
                        'order_reference_id' => $orderReferenceDetails->amazon_order_reference_id,
                    ]));

                default:
                    throw new FatalException(__('error.amazon_pay_order_amount_not_set', [
                        'order_reference_id' => $orderReferenceDetails->amazon_order_reference_id,
                        'constraint' => json_encode($constraint->toArray()),
                    ]));
            }
        }
    }

    /**
     * キャプチャを実行する
     *
     * @param int $orderId
     *
     * @return array
     */
    public function capture(int $orderId)
    {
        $amazonPayOrder = $this->amazonPayOrderRepository->findWhere([
            'order_id' => $orderId,
        ])->first();

        $amazonPayOrder->load(['authorizations.captures']);

        if (empty($amazonPayOrder)) {
            throw (new ModelNotFoundException(__('error.model_not_found')))->setModel(\App\Models\AmazonPayOrder::class);
        }

        $stored = (new \App\Models\AmazonPayCapture())->newCollection();
        $failedReports = [];

        foreach ($amazonPayOrder->authorizations as $authorization) {
            try {
                // キャプチャ済みは除外する。
                // 現状、autorizationに対して実質一対一なのでcaptureが存在すれば売上済み。
                if ($authorization->captures->isNotEmpty()) {
                    continue;
                }

                if ($authorization->status === \App\Enums\AmazonPay\Status\Authorization::Closed) {
                    continue;
                }

                $authorizationDetails = $this->amazonPayAdapter->getAuthorizationDetails($authorization->amazon_authorization_id);

                if ($authorization->capturing_amount === 0) {
                    if ($authorizationDetails->authorization_status->state !== \App\Enums\AmazonPay\Status\Authorization::Closed) {
                        $authorization = $this->closeAuthorization($authorization);
                    }
                    continue;
                }

                DB::beginTransaction();

                // オーソリが有効かどうかを検証する。Openではない場合、再度オーソリできるか試す。
                if ($authorizationDetails->authorization_status->state !== \App\Enums\AmazonPay\Status\Authorization::Open) {
                    $authorization = $this->authorize($amazonPayOrder->order_reference_id, $amazonPayOrder->id, $authorization->capturing_amount);
                    $this->importAuthorizationDetails($authorizationDetails, true);
                    DB::commit();
                    DB::beginTransaction();
                }

                $captureReferenceId = $this->captureRepository->generateReferenceId();

                $captureDetails = $this->amazonPayAdapter->capture(
                    $authorization->amazon_authorization_id,
                    $captureReferenceId,
                    $authorization->capturing_amount
                );

                // 部分キャンセルが発生した場合、全額請求するまで有効期間内はオーソリが有効になり続けるため手動でクローズする
                $skipSyncAmazonPay = (int) $authorization->capturing_amount === (int) $authorization->amount;
                $authorization = $this->closeAuthorization($authorization, $skipSyncAmazonPay);

                if ($captureDetails->capture_status->state === \App\Enums\AmazonPay\Status\Capture::Declined) {
                    throw new FatalException(__('error.amazon_pay_capture_declined', [
                        'reason_code' => $captureDetails->capture_status->reason_code,
                        'reason_description' => $captureDetails->capture_status->reason_description,
                    ]));
                }

                $capture = $this->createCapture($captureDetails, $authorization);

                $stored->add($capture);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                report($e);

                $failedReports[] = [
                    'authorization' => $authorization,
                    'exception' => $e,
                ];
            }
        }

        if (empty($failedReports)) {
            $amazonPayOrder = $this->closeOrderReference($amazonPayOrder);
        }

        return [
            'stored' => $stored,
            'failed_reports' => $failedReports,
        ];
    }

    /**
     * OrderReferenceの手動クローズ
     *
     * @param \App\Models\AmazonPayOrder $amazonPayOrder
     *
     * @return \App\Models\AmazonPayOrder
     */
    private function closeOrderReference(\App\Models\AmazonPayOrder $amazonPayOrder)
    {
        $this->amazonPayAdapter->closeOrderReference($amazonPayOrder->order_reference_id);

        $amazonPayOrder = $this->amazonPayOrderRepository->update([
            'status' => \App\Enums\AmazonPay\Status\OrderReference::Closed,
        ], $amazonPayOrder->id);

        return $amazonPayOrder;
    }

    /**
     * オーソリの手動クローズ
     *
     * @param \App\Models\AmazonPayAuthorization $authorization
     *
     * @return \App\Models\AmazonPayAuthorization
     */
    private function closeAuthorization(\App\Models\AmazonPayAuthorization $authorization, ?bool $skipSyncAmazonPay = false)
    {
        if ($skipSyncAmazonPay === false) {
            $this->amazonPayAdapter->closeAuthorization($authorization->amazon_authorization_id);
        }

        $authorization = $this->authorizationRepository->update([
            'status' => \App\Enums\AmazonPay\Status\Authorization::Closed,
        ], $authorization->id);

        return $authorization;
    }

    /**
     * amazon_pay_capturesを作成する
     *
     * @param \App\Entities\AmazonPay\CaptureDetails $captureDetails
     * @param \App\Models\AmazonPayAuthorization $authorization
     *
     * @return \App\Models\AmazonPayCapture
     */
    public function createCapture(
        \App\Entities\AmazonPay\CaptureDetails $captureDetails,
        \App\Models\AmazonPayAuthorization $authorization
    ) {
        return $this->captureRepository->create([
            'capture_reference_id' => $captureDetails->capture_reference_id,
            'amazon_pay_authorization_id' => $authorization->id,
            'amazon_capture_id' => $captureDetails->amazon_capture_id,
            'status' => $captureDetails->capture_status->state,
            'status_reason_code' => $captureDetails->capture_status->reason_code,
            'last_status_updated_at' => $captureDetails->capture_status->last_update_timestamp,
            'amount' => $captureDetails->capture_amount->amount,
            'fee' => $captureDetails->capture_fee->amount,
        ]);
    }

    /**
     * amazon_pay_capturesを更新する
     *
     * @param \App\Entities\AmazonPay\CaptureDetails $captureDetails
     *
     * @return \App\Models\AmazonPayCapture
     */
    public function importCaptureDetails(\App\Entities\AmazonPay\CaptureDetails $captureDetails)
    {
        $capture = $this->captureRepository->findWhere([
            'capture_reference_id' => $captureDetails->capture_reference_id,
            'amazon_capture_id' => $captureDetails->amazon_capture_id,
        ])->first();

        if (empty($capture)) {
            throw (new ModelNotFoundException(error_format('error.model_not_found', [
                'capture_reference_id' => $captureDetails->capture_reference_id,
                'amazon_capture_id' => $captureDetails->amazon_capture_id,
            ])))->setModel(\App\Models\AmazonPayCapture::class);
        }

        return $this->captureRepository->update([
            'status' => $captureDetails->capture_status->state,
            'status_reason_code' => $captureDetails->capture_status->reason_code,
            'last_status_updated_at' => $captureDetails->capture_status->last_update_timestamp,
            'amount' => $captureDetails->capture_amount->amount,
            'fee' => $captureDetails->capture_fee->amount,
        ], $capture->id);
    }

    /**
     * 注文のキャンセル
     *
     * @param int $orderId
     *
     * @return \App\Models\AmazonPayOrder
     */
    public function cancelOrder(int $orderId)
    {
        $amazonPayOrder = $this->amazonPayOrderRepository->findWhere([
            'order_id' => $orderId,
        ])->first();

        if (empty($amazonPayOrder)) {
            throw (new ModelNotFoundException(error_format('error.model_not_found', [
                'order_id' => $orderId,
            ])))->setModel(\App\Models\AmazonPayOrder::class);
        }

        $this->amazonPayAdapter->cancelOrderReference($amazonPayOrder->order_reference_id);

        $amazonPayOrder = $this->amazonPayOrderRepository->update([
            'status' => \App\Enums\AmazonPay\Status\OrderReference::Canceled,
        ], $amazonPayOrder->id);

        return $amazonPayOrder;
    }

    /**
     * オーソリした金額を変更する
     *
     * @param int $orderId
     * @param int $newAmount
     *
     * @return \App\Models\AmazonPayOrder
     */
    public function changeAuthorizationAmount(int $orderId, int $newAmount)
    {
        $amazonPayOrder = $this->amazonPayOrderRepository->findWhere([
            'order_id' => $orderId,
        ])->first();

        $amazonPayOrder->load(['authorizations' => function ($query) {
            return $query->where('status', '!=', \App\Enums\AmazonPay\Status\Authorization::Closed);
        }]);

        if (empty($amazonPayOrder)) {
            throw (new ModelNotFoundException(error_format('error.model_not_found', [
                'order_id' => $orderId,
            ])))->setModel(\App\Models\AmazonPayOrder::class);
        }

        $capturingAmount = $amazonPayOrder->authorizations->sum('capturing_amount');

        $reducing = $newAmount < $capturingAmount;

        if ($reducing) {
            $amazonPayOrder = $this->reduceAuthorizationAmount($amazonPayOrder, $newAmount);
        } else {
            $amazonPayOrder = $this->addAmountOrCreateAuthorization($amazonPayOrder, $newAmount);
        }

        return $amazonPayOrder;
    }

    /**
     * 認証の減算処理
     *
     * @param \App\Models\AmazonPayOrder $amazonPayOrder
     * @param int $newAmount
     *
     * @return \App\Models\AmazonPayOrder
     */
    private function reduceAuthorizationAmount(\App\Models\AmazonPayOrder $amazonPayOrder, int $newAmount)
    {
        $reducingAmount = $amazonPayOrder->authorizations->sum('capturing_amount') - $newAmount;

        foreach ($amazonPayOrder->authorizations as $authorization) {
            $diffAmount = min($authorization->capturing_amount, $reducingAmount);

            $reducingAmount -= $diffAmount;

            $this->authorizationRepository->update([
                'capturing_amount' => $authorization->capturing_amount - $diffAmount,
            ], $authorization->id);

            if ($reducingAmount === 0) {
                break;
            }
        }

        $amazonPayOrder->load('authorizations');

        return $amazonPayOrder;
    }

    /**
     * 請求予定金額の加算と追加のオーソリを実行
     *
     * @param \App\Models\AmazonPayOrder $amazonPayOrder
     * @param int $newAmount
     *
     * @return \App\Models\AmazonPayOrder
     */
    private function addAmountOrCreateAuthorization(\App\Models\AmazonPayOrder $amazonPayOrder, int $newAmount)
    {
        $roomAmount = $amazonPayOrder->authorizations->sum('amount')
            - $amazonPayOrder->authorizations->sum('capturing_amount');

        if ($roomAmount > 0) {
            $amazonPayOrder = $this->addAuthorizationAmount($amazonPayOrder, $newAmount);
        }

        $unresolvedAmount = $newAmount
            - $amazonPayOrder->authorizations->sum('capturing_amount');

        if ($unresolvedAmount > 0) {
            $this->authorize($amazonPayOrder->order_reference_id, $amazonPayOrder->id, $unresolvedAmount);
            $amazonPayOrder->load('authorizations');
        }

        return $amazonPayOrder;
    }

    /**
     * 認証の減算処理
     *
     * @param \App\Models\AmazonPayOrder $amazonPayOrder
     * @param int $newAmount
     *
     * @return \App\Models\AmazonPayOrder
     */
    private function addAuthorizationAmount(\App\Models\AmazonPayOrder $amazonPayOrder, int $newAmount)
    {
        $addingAmount = $newAmount;

        foreach ($amazonPayOrder->authorizations as $authorization) {
            if ((int) $authorization->amount === (int) $authorization->capturing_amount) {
                continue;
            }

            $newCapturingAmount = min($authorization->amount, $addingAmount);

            $addingAmount -= $newCapturingAmount;

            $this->authorizationRepository->update([
                'capturing_amount' => $newCapturingAmount,
            ], $authorization->id);

            if ($addingAmount === 0) {
                break;
            }
        }

        $amazonPayOrder->load('authorizations');

        return $amazonPayOrder;
    }

    /**
     * 返金処理
     *
     * @param int $orderId
     * @param int $amount
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refund(int $orderId, int $amount)
    {
        $portion = $this->getPortionedRefundingCaptures($orderId, $amount);

        foreach ($portion as $params) {
            $refundingCapture = $params['capture'];

            $refundReferenceId = $this->refundRepository->generateReferenceId();

            $refundDetails = $this->amazonPayAdapter->refund(
                $refundingCapture->amazon_capture_id,
                $refundReferenceId,
                $params['amount']
            );

            $this->importRefund($refundingCapture->id, $refundDetails);
        }

        $refunds = $this->refundRepository->findWhere([
            'amazon_pay_capture_id' => $refundingCapture->amazon_capture_id,
        ]);

        return $refunds;
    }

    /**
     * 返金に使用するcaputureデータと各captureに割り当てた返金額を返す
     *
     * @param int $orderId
     * @param int $amount
     *
     * @return array
     */
    private function getPortionedRefundingCaptures(int $orderId, int $amount)
    {
        $amazonPayOrder = $this->amazonPayOrderRepository->findOrFail(['order_id' => $orderId]);
        $amazonPayOrder->load(['authorizations.captures']);
        $portion = [];
        $computingAmount = $amount;

        foreach ($amazonPayOrder->authorizations as $authorization) {
            foreach ($authorization->captures as $capture) {
                $assigningAmount = min($computingAmount, $capture->amount);
                $computingAmount -= $assigningAmount;

                $portion[] = [
                    'capture' => $capture,
                    'amount' => $assigningAmount,
                ];

                if ($computingAmount === 0) {
                    return $portion;
                }
            }
        }

        throw new AmazonPayInsufficientRefundingCaptureException(
            error_format('error.amazon_pay_insufficient_refunding_capture', [
                'order_id' => $orderId,
                'amount' => $amount,
            ])
        );
    }

    /**
     * @param int $amazonPayCaptureId
     * @param \App\Entities\AmazonPay\RefundDetails $refundDetails
     *
     * @return \App\Models\AmazonPayRefund
     */
    private function importRefund(int $amazonPayCaptureId, \App\Entities\AmazonPay\RefundDetails $refundDetails)
    {
        return $this->refundRepository->create([
            'amazon_pay_capture_id' => $amazonPayCaptureId,
            'refund_reference_id' => $refundDetails->refund_reference_id,
            'amazon_refund_id' => $refundDetails->amazon_refund_id,
            'status' => $refundDetails->refund_status->state,
            'status_reason_code' => $refundDetails->refund_status->reason_code,
            'last_status_updated_at' => $refundDetails->refund_status->last_update_timestamp,
            'amount' => $refundDetails->refund_amount->amount,
            'fee' => $refundDetails->fee_refunded->amount,
        ]);
    }
}
