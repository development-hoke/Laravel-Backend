<?php

namespace App\HttpCommunication\AmazonPay\Exceptions;

/**
 * リクエストの実行に失敗
 */
class FailedRequestExecption extends Exception
{
    /**
     * @var array
     */
    private $requestData;

    /**
     * @param \Exception $e
     * @param array|null $requestData
     * @param string $message
     */
    public function __construct(\Exception $e, ?array $requestData = [], string $message = null)
    {
        parent::__construct($message ?? $this->buildMessage($e, $requestData), $e->getCode(), $e);

        $this->requestData = $requestData;
    }

    /**
     * @return array
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * @param array $requestData
     * @param array $info
     *
     * @return string
     */
    private function buildMessage(\Exception $e, array $requestData)
    {
        return __('error.amazon_pay_failed_request', [
            'original_message' => $e->getMessage(),
            'method' => $requestData['method'],
            'params' => json_encode($requestData['params']),
        ]);
    }
}
