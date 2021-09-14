<?php

namespace App\HttpCommunication\AmazonPay\Mock;

use App\HttpCommunication\AmazonPay\HttpCommunication as HttpCommunicationInterface;
use App\HttpCommunication\Response\Mock\Response;

class HttpCommunication implements HttpCommunicationInterface
{
    /**
     * アクセストークンからユーザー情報を取得する
     * このリクエストのみ、他のリクエストと処理系が異なるので個別で定義する。
     *
     * @param string $accessToken
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getUserInfo(string $accessToken)
    {
        return new Response([]);
    }

    /**
     * 注文情報をセットする
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function setOrderReferenceDetails(string $orderReferenceId, int $amount, ?array $options = [])
    {
        return new Response([]);
    }

    /**
     * 注文をOpenステータスに変更し、オーソリを可能な状態にする
     *
     * @param string $orderReferenceId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function confirmOrderReference(string $orderReferenceId, ?array $options = [])
    {
        return new Response([]);
    }

    /**
     * オーソリの実行
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authorize(string $orderReferenceId, string $authorizationReferenceId, int $amount, ?array $options = [])
    {
        $mock = require __DIR__.'/fixtures/authorize_result_open.php';

        $mock['AuthorizeResult']['AuthorizationDetails']['AuthorizationReferenceId'] = $authorizationReferenceId;
        $mock['AuthorizeResult']['AuthorizationDetails']['AuthorizationAmount']['Amount'] = $amount;

        return new Response($mock);
    }

    /**
     * 売上を確定する
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function capture(string $amazonAuthorizationId, string $captureReferenceId, int $captureAmount, ?array $options = [])
    {
        $mock = require __DIR__.'/fixtures/capture_result_completed.php';
        $mock['CaptureResult']['CaptureDetails']['CaptureReferenceId'] = $captureReferenceId;
        $mock['CaptureResult']['CaptureDetails']['CaptureAmount']['Amount'] = $captureAmount;

        return new Response($mock);
    }

    /**
     * OrderReferenceをキャンセルする。
     *
     * @param string $orderReferenceId
     * @param string|null $reason
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function cancelOrderReference(string $orderReferenceId, ?string $reason = null, ?array $options = [])
    {
        return new Response([]);
    }

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
    public function refund(string $amazonCaptureId, string $refundReferenceId, int $amount, ?array $options = [])
    {
        return new Response([]);
    }

    /**
     * OrderReferenceを手動でクローズする
     *
     * @param string $orderReferenceId
     * @param string|null $reason 最大1024文字
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function closeOrderReference(string $orderReferenceId, ?string $reason = null, ?array $options = [])
    {
        return new Response([]);
    }

    /**
     * Authorizationを手動でクローズする
     *
     * @param string $amazonAuthorizationId
     * @param string|null $reason
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function closeAuthorization(string $amazonAuthorizationId, ?string $reason = null, ?array $options = [])
    {
        return new Response([]);
    }

    /**
     * Order Referenceオブジェクトの詳細と現在の状態を取得
     *
     * @param string $orderReferenceId
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getOrderReferenceDetails(string $orderReferenceId, ?array $options = [])
    {
        return new Response([]);
    }

    /**
     * オーソリ情報詳細取得
     *
     * @param string $amazonAuthorizationId
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getAuthorizationDetails(string $amazonAuthorizationId, ?array $options = [])
    {
        $mock = require __DIR__.'/fixtures/get_authorization_details_result_closed.php';
        $mock['AuthorizeResult']['AuthorizationDetails']['AmazonAuthorizationId'] = $amazonAuthorizationId;

        return new Response($mock);
    }

    /**
     * 売上請求詳細取得
     *
     * @param string $amazonCaptureId
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getCaptureDetails(string $amazonCaptureId, ?array $options = [])
    {
        return new Response([]);
    }

    /**
     * 返金詳細取得
     *
     * @param string $amazonRefundId
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getRefundDetails(string $amazonRefundId, ?array $options = [])
    {
        return new Response([]);
    }
}
