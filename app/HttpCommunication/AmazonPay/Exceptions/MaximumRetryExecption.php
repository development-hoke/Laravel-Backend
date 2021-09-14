<?php

namespace App\HttpCommunication\AmazonPay\Exceptions;

/**
 * リクエストの再試行回数を超える
 */
class MaximumRetryExecption extends Exception
{
    /**
     * 試行回数
     *
     * @var int|null
     */
    public $retries;

    /**
     * 最後に失敗したときに取得されたHTTPステータスコード
     *
     * @var int|null
     */
    public $statusCode;

    /**
     * @param \Exception $e
     */
    public function __construct(\Exception $e, array $requestData)
    {
        $info = $this->parseMessage($e->getMessage());

        $message = $this->buildMessage($requestData, $info);

        parent::__construct($message, $e->getCode(), $e);

        $this->setParsedInfo($info);
    }

    /**
     * @param string $message
     *
     * @return array
     */
    private function parseMessage(string $message)
    {
        $info = [];

        if (preg_match('/Error Code: (\d+)/', $message, $matches)) {
            $info['status_code'] = (int) $matches[1];
        }

        if (preg_match('/Maximum number of retry attempts - (\d+) reached/', $message, $matches)) {
            $info['retries'] = (int) $matches[1];
        }

        return $info;
    }

    /**
     * @param array $info
     *
     * @return static
     */
    private function setParsedInfo(array $info)
    {
        $this->statusCode = $info['status_code'] ?? null;
        $this->retries = $info['retries'] ?? null;

        return $this;
    }

    /**
     * @param array $requestData
     * @param array $info
     *
     * @return string
     */
    private function buildMessage(array $requestData, array $info)
    {
        return __('error.amazon_pay_maximum_retry', [
            'method' => $requestData['method'],
            'params' => json_encode($requestData['params']),
            'status_code' => $info['status_code'] ?? '',
            'retries' => $info['retries'] ?? '',
        ]);
    }
}
