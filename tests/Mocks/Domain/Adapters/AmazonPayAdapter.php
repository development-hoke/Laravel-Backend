<?php

namespace Tests\Mocks\Domain\Adapters;

use App\Domain\Adapters\AmazonPayAdapterInterface;

class AmazonPayAdapter implements AmazonPayAdapterInterface
{
    /**
     * @var array
     */
    public $getAuthorizationDetailsResults = [];

    /**
     * @var array
     */
    public $getOrderReferenceDetailsResults = [];

    /**
     * @var array
     */
    public $setOrderReferenceDetailsResults = [];

    /**
     * @var array
     */
    public $authorizeResults = [];

    /**
     * @var array
     */
    public $captureResults = [];

    /**
     * @var array
     */
    public $orderReferenceDetails = [];

    /**
     * @var array
     */
    public $setOrderReferenceDetailsRequestParams = [];

    /**
     * @var array
     */
    public $captureRequestParams = [];

    /**
     * @var array
     */
    public $authorizeRequestParams = [];

    /**
     * @var array
     */
    public $closeAuthorizationRequestParams = [];

    /**
     * @var array
     */
    public $closeOrderReferenceRequestParams = [];

    /**
     * @var array
     */
    public $cancelOrderReferenceRequestParams = [];

    /**
     * @var array
     */
    public $refundRequestParams = [];

    /**
     * 注文情報をセットする
     *
     * @param array $params
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function setOrderReferenceDetails(string $orderReferenceId, int $amount, ?array $options = [])
    {
        $this->setOrderReferenceDetailsRequestParams[] = func_get_args();

        $orderReferenceDetails = $this->setOrderReferenceDetailsResults[$orderReferenceId] ?? $this->setOrderReferenceDetailsResults;

        return new \App\Entities\AmazonPay\OrderReferenceDetails($orderReferenceDetails);
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
        $this->authorizeRequestParams[] = func_get_args();

        $authorizeResults = $this->authorizeResults[$orderReferenceId] ?? $this->authorizeResults;

        return new \App\Entities\AmazonPay\AuthorizationDetails($authorizeResults);
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
        $this->captureRequestParams[] = func_get_args();

        $captureDetails = $this->captureResults[$amazonAuthorizationId] ?? $this->captureResults;

        return new \App\Entities\AmazonPay\CaptureDetails($captureDetails);
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
        $orderReferenceDetails = $this->getOrderReferenceDetailsResults[$orderReferenceId] ?? $this->getOrderReferenceDetailsResults;

        return new \App\Entities\AmazonPay\OrderReferenceDetails($orderReferenceDetails);
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
        $authorizationDetails = $this->getAuthorizationDetailsResults[$amazonAuthorizationId] ?? $this->getAuthorizationDetailsResults;

        return new \App\Entities\AmazonPay\AuthorizationDetails($authorizationDetails);
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
        $this->cancelOrderReferenceRequestParams[] = func_get_args();

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
        $this->closeOrderReferenceRequestParams[] = func_get_args();

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
        $this->closeAuthorizationRequestParams[] = func_get_args();

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
        $this->refundRequestParams[] = func_get_args();

        $refundResults = $this->refundResults[$amazonCaptureId] ?? $this->refundResults;

        return new \App\Entities\AmazonPay\RefundDetails(
            $refundResults
        );
    }
}
