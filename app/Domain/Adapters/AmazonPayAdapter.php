<?php

namespace App\Domain\Adapters;

use App\HttpCommunication\AmazonPay\HttpCommunication as AmazonPayHttpCommunication;

class AmazonPayAdapter implements AmazonPayAdapterInterface
{
    const SYNC_MODE_TRANSACTION_TIMEOUT = 0;

    /**
     * @var \App\HttpCommunication\AmazonPay\HttpCommunication
     */
    private $httpCommunication;

    /**
     * @param AmazonPayHttpCommunication $httpCommunication
     */
    public function __construct(AmazonPayHttpCommunication $httpCommunication)
    {
        $this->httpCommunication = $httpCommunication;
    }

    /**
     * 注文情報をセットする
     *
     * @param array $params
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function setOrderReferenceDetails(string $orderReferenceId, int $amount, ?array $options = [])
    {
        $response = $this->httpCommunication->setOrderReferenceDetails($orderReferenceId, $amount, $options);

        $body = $response->getBody();

        return new \App\Entities\AmazonPay\OrderReferenceDetails(
            $body['SetOrderReferenceDetailsResult']['OrderReferenceDetails']
        );
    }

    /**
     * 注文をOpenステータスに変更し、オーソリを可能な状態にする
     *
     * @param string $orderReferenceId
     *
     * @return bool
     */
    public function confirmOrderReference(string $orderReferenceId, ?array $options = [])
    {
        $this->httpCommunication->confirmOrderReference($orderReferenceId, $options);

        return true;
    }

    /**
     * オーソリの実行
     *
     * @param string $orderReferenceId
     * @param string $authorizationReferenceId
     * @param int $amount
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
    ) {
        if ($sync === true) {
            $options['transaction_timeout'] = self::SYNC_MODE_TRANSACTION_TIMEOUT;
        }

        $response = $this->httpCommunication->authorize($orderReferenceId, $authorizationReferenceId, $amount, $options);

        $body = $response->getBody();

        return new \App\Entities\AmazonPay\AuthorizationDetails(
            $body['AuthorizeResult']['AuthorizationDetails']
        );
    }

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
    public function capture(string $amazonAuthorizationId, string $captureReferenceId, int $captureAmount, ?array $options = [])
    {
        $response = $this->httpCommunication->capture($amazonAuthorizationId, $captureReferenceId, $captureAmount, $options);

        $body = $response->getBody();

        return new \App\Entities\AmazonPay\CaptureDetails(
            $body['CaptureResult']['CaptureDetails']
        );
    }

    /**
     * Order Referenceオブジェクトの詳細と現在の状態を取得
     *
     * @param string $orderReferenceId
     * @param array|null $options ['access_token' => string]
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function getOrderReferenceDetails(string $orderReferenceId, ?array $options = [])
    {
        $response = $this->httpCommunication->getOrderReferenceDetails($orderReferenceId, $options);

        $body = $response->getBody();

        return new \App\Entities\AmazonPay\OrderReferenceDetails(
            $body['GetOrderReferenceDetailsResult']['OrderReferenceDetails']
        );
    }

    /**
     * オーソリ詳細情報取得
     *
     * @param string $amazonAuthorizationId
     * @param array|null $options
     *
     * @return \App\Entities\AmazonPay\AuthorizationDetails
     */
    public function getAuthorizationDetails(string $amazonAuthorizationId, ?array $options = [])
    {
        $response = $this->httpCommunication->getAuthorizationDetails($amazonAuthorizationId, $options);

        $body = $response->getBody();

        return new \App\Entities\AmazonPay\AuthorizationDetails(
            $body['GetAuthorizationDetailsResult']['AuthorizationDetails']
        );
    }

    /**
     * OrderReferenceをキャンセルする。
     *
     * @param string $orderReferenceId
     * @param string|null $reason
     * @param array|null $options
     *
     * @return bool
     */
    public function cancelOrderReference(string $orderReferenceId, ?string $reason = null, ?array $options = [])
    {
        $this->httpCommunication->cancelOrderReference($orderReferenceId, $reason, $options);

        return true;
    }

    /**
     * OrderReferenceをクローズにする。
     *
     * @param string $orderReferenceId
     * @param string|null $reason
     * @param array|null $options
     *
     * @return bool
     */
    public function closeOrderReference(string $orderReferenceId, ?string $reason = null, ?array $options = [])
    {
        $this->httpCommunication->closeOrderReference($orderReferenceId, $reason, $options);

        return true;
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
        $this->httpCommunication->closeAuthorization($amazonAuthorizationId, $reason, $options);

        return true;
    }

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
    public function refund(string $amazonCaptureId, string $refundReferenceId, int $amount, ?array $options = [])
    {
        $response = $this->httpCommunication->refund($amazonCaptureId, $refundReferenceId, $amount, $options);

        $body = $response->getBody();

        return new \App\Entities\AmazonPay\RefundDetails(
            $body['RefundResult']['RefundDetails']
        );
    }
}
