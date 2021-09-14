<?php

namespace App\HttpCommunication\AmazonPay\Concrete;

use AmazonPay\Client;
use AmazonPay\ClientInterface;
use App\Exceptions\FatalException;
use App\HttpCommunication\AmazonPay\Exceptions\Handler;
use App\HttpCommunication\AmazonPay\Exceptions\HttpException;
use App\HttpCommunication\AmazonPay\GetUserInfoResponse;
use App\HttpCommunication\AmazonPay\HttpCommunication as HttpCommunicationInterface;
use App\HttpCommunication\AmazonPay\Response;

class HttpCommunication implements HttpCommunicationInterface
{
    /**
     * @var \AmazonPay\ClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $storeName;

    /**
     * @param \AmazonPay\ClientInterface $client
     */
    public function __construct(ClientInterface $client = null, $config = null)
    {
        $this->setConfig($config);

        $config = $this->config;

        $this->client = $client ?? new Client([
            'merchant_id' => $config['merchant_id'],
            'access_key' => $config['access_key'],
            'secret_key' => $config['secret_key'],
            'client_id' => $config['client_id'],
            'region' => $config['region'],
            'currency_code' => $config['currency_code'],
            'sandbox' => $config['sandbox'],
        ]);

        $this->storeName = $config['store_name'];
    }

    /**
     * @param array|null $config
     *
     * @return static
     */
    private function setConfig(array $config = null)
    {
        $this->config = $config ?? config('http_communication.amazon_pay');

        return $this;
    }

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
        try {
            $results = $this->client->getUserInfo($accessToken);

            $response = new GetUserInfoResponse($results);

            return $response;
        } catch (\Exception $e) {
            (new Handler())->handleGetUserInfoError($e, 'getUserInfo');
        }
    }

    /**
     * 注文情報をセットする
     *
     * @param array $params
     *
     * @return array
     */
    public function setOrderReferenceDetails(string $orderReferenceId, int $amount, ?array $options = [])
    {
        return $this->sendRequest('setOrderReferenceDetails', array_merge($options, [
            'amazon_order_reference_id' => $orderReferenceId,
            'amount' => $amount,
            'store_name' => $this->storeName,
        ]));
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
        return $this->sendRequest('confirmOrderReference', array_merge($options, [
            'amazon_order_reference_id' => $orderReferenceId,
        ]));
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
        $params = [
            'amazon_order_reference_id' => $orderReferenceId,
            'authorization_reference_id' => $authorizationReferenceId,
            'authorization_amount' => $amount,
        ];

        return $this->sendRequest('authorize', array_merge($options, $params));
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
        $params = [
            'amazon_authorization_id' => $amazonAuthorizationId,
            'capture_reference_id' => $captureReferenceId,
            'capture_amount' => $captureAmount,
        ];

        return $this->sendRequest('capture', array_merge($options, $params));
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
        $params = [
            'amazon_order_reference_id' => $orderReferenceId,
        ];

        if (isset($reason)) {
            $params['closure_reason'] = $reason;
        }

        return $this->sendRequest('cancelOrderReference', array_merge($options, $params));
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
        $params = [
            'amazon_capture_id' => $amazonCaptureId,
            'refund_reference_id' => $refundReferenceId,
            'refund_amount' => $amount,
        ];

        return $this->sendRequest('refund', array_merge($options, $params));
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
        $params = [
            'amazon_order_reference_id' => $orderReferenceId,
        ];

        if (isset($reason)) {
            $params['closure_reason'] = $reason;
        }

        return $this->sendRequest('closeOrderReference', array_merge($options, $params));
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
        $params = ['amazon_authorization_id' => $amazonAuthorizationId];

        if (isset($reason)) {
            $params['closure_reason'] = $reason;
        }

        return $this->sendRequest('closeAuthorization', array_merge($options, $params));
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
        $params = [
            'amazon_order_reference_id' => $orderReferenceId,
        ];

        return $this->sendRequest('getOrderReferenceDetails', array_merge($options, $params));
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
        $params = ['amazon_authorization_id' => $amazonAuthorizationId];

        return $this->sendRequest('getAuthorizationDetails', array_merge($options, $params));
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
        $params = ['amazon_capture_id' => $amazonCaptureId];

        return $this->sendRequest('getCaptureDetails', array_merge($options, $params));
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
        $params = ['amazon_refund_id' => $amazonRefundId];

        return $this->sendRequest('getRefundDetails', array_merge($options, $params));
    }

    /**
     * @param string $method
     * @param array $params
     *
     * @return Response
     */
    private function sendRequest(string $method, array $params)
    {
        try {
            if (!method_exists($this->client, $method)) {
                throw new FatalException(error_format('error.amazon_pay_method_does_not_exists'));
            }

            $results = $this->client->{$method}($params);

            $response = new Response($results);

            if (!$this->isSuccessfulResponse($response)) {
                throw new HttpException($response);
            }

            return $response;
        } catch (\Exception $e) {
            (new Handler())->handle($e, $method, $params);
        }
    }

    /**
     * 成功レスポンスの定義
     *
     * @param Response $response
     *
     * @return bool
     */
    private function isSuccessfulResponse(Response $response)
    {
        return $response->getStatusCode() < 400;
    }
}
