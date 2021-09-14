<?php

namespace App\HttpCommunication\AmazonPay;

interface HttpCommunication
{
    /**
     * アクセストークンからユーザー情報を取得する
     * このリクエストのみ、他のリクエストと処理系が異なるので個別で定義する。
     *
     * @param string $accessToken
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getUserInfo(string $accessToken);

    /**
     * 注文情報をセットする
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function setOrderReferenceDetails(string $orderReferenceId, int $amount, ?array $options = []);

    /**
     * 注文をOpenステータスに変更し、オーソリを可能な状態にする
     *
     * @param string $orderReferenceId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function confirmOrderReference(string $orderReferenceId, ?array $options = []);

    /**
     * オーソリの実行
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authorize(string $orderReferenceId, string $authorizationReferenceId, int $amount, ?array $options = []);

    /**
     * 売上を確定する
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function capture(string $amazonAuthorizationId, string $captureReferenceId, int $captureAmount, ?array $options = []);

    /**
     * OrderReferenceをキャンセルする。
     *
     * @param string $orderReferenceId
     * @param string|null $reason
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function cancelOrderReference(string $orderReferenceId, ?string $reason = null, ?array $options = []);

    /**
     * 返金処理
     *
     * @param string $amazonCaptureId
     * @param string $refundReferenceId
     * @param int $amount 返金金額
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function refund(string $amazonCaptureId, string $refundReferenceId, int $amount, ?array $options = []);

    /**
     * OrderReferenceを手動でクローズする
     *
     * @param string $orderReferenceId
     * @param string|null $reason 最大1024文字
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
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
     * Order Referenceオブジェクトの詳細と現在の状態を取得
     *
     * @param string $orderReferenceId
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getOrderReferenceDetails(string $orderReferenceId, ?array $options = []);

    /**
     * オーソリ情報詳細取得
     *
     * @param string $amazonAuthorizationId
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getAuthorizationDetails(string $amazonAuthorizationId, ?array $options = []);

    /**
     * 売上請求詳細取得
     *
     * @param string $amazonCaptureId
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getCaptureDetails(string $amazonCaptureId, ?array $options = []);

    /**
     * 返金詳細取得
     *
     * @param string $amazonRefundId
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getRefundDetails(string $amazonRefundId, ?array $options = []);
}
