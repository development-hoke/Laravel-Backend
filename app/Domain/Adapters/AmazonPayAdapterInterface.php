<?php

namespace App\Domain\Adapters;

interface AmazonPayAdapterInterface
{
    /**
     * 注文情報をセットする
     *
     * @param array $params
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function setOrderReferenceDetails(string $orderReferenceId, int $amount, ?array $options = []);

    /**
     * 注文をOpenステータスに変更し、オーソリを可能な状態にする
     *
     * @param string $orderReferenceId
     *
     * @return bool
     */
    public function confirmOrderReference(string $orderReferenceId, ?array $options = []);

    /**
     * オーソリの実行
     *
     * @param string $orderReferenceId
     * @param string $authorizationReferenceId
     * @param int $amount
     * @param bool|null $sync 同期モードの選択。通常はデフォルトはTrue。
     * @param array|null $options
     *
     * @return \App\Entities\AmazonPay\AuthorizationDetails
     */
    public function authorize(
        string $orderReferenceId,
        string $authorizationReferenceId,
        int $amount,
        ?bool $sync = true,
        ?array $options = []
    );

    /**
     * 売上を確定する
     *
     * @param string $amazonAuthorizationId
     * @param string $captureReferenceId
     * @param int $captureAmount
     * @param array|null $options
     *
     * @return \App\Entities\AmazonPay\CaptureDetails
     */
    public function capture(string $amazonAuthorizationId, string $captureReferenceId, int $captureAmount, ?array $options = []);

    /**
     * Order Referenceオブジェクトの詳細と現在の状態を取得
     *
     * @param string $orderReferenceId
     * @param array|null $options ['access_token' => string]
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function getOrderReferenceDetails(string $orderReferenceId, ?array $options = []);

    /**
     * オーソリ詳細情報取得
     *
     * @param string $amazonAuthorizationId
     * @param array|null $options
     *
     * @return \App\Entities\AmazonPay\AuthorizationDetails
     */
    public function getAuthorizationDetails(string $amazonAuthorizationId, ?array $options = []);

    /**
     * OrderReferenceをキャンセルする。
     *
     * @param string $orderReferenceId
     * @param string|null $reason
     * @param array|null $options
     *
     * @return bool
     */
    public function cancelOrderReference(string $orderReferenceId, ?string $reason = null, ?array $options = []);

    /**
     * OrderReferenceをクローズにする。
     *
     * @param string $orderReferenceId
     * @param string|null $reason
     * @param array|null $options
     *
     * @return bool
     */
    public function closeOrderReference(string $orderReferenceId, ?string $reason = null, ?array $options = []);

    /**
     * Authorizationを手動でクローズする
     *
     * @param string $amazonAuthorizationId
     * @param string|null $reason
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function closeAuthorization(string $amazonAuthorizationId, ?string $reason = null, ?array $options = []);

    /**
     * 返金
     *
     * @param string $amazonCaptureId
     * @param string $refundReferenceId
     * @param int $amount
     * @param array|null $options
     *
     * @return \App\Entities\AmazonPay\RefundDetails
     */
    public function refund(string $amazonCaptureId, string $refundReferenceId, int $amount, ?array $options = []);
}
