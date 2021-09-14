<?php

namespace App\HttpCommunication\AmazonPay\Exceptions;

class Handler
{
    /**
     * 異常系処理
     *
     * @param \Exception $e
     * @param string $method
     * @param array $requestParams
     *
     * @return void
     *
     * @throws \App\HttpCommunication\AmazonPay\Exceptions\Exception
     */
    public function handle(\Exception $e, string $method, array $requestParams = [])
    {
        if ($e instanceof HttpException) {
            throw $e;
        }

        if ($this->isMaximumRetryError($e)) {
            throw new MaximumRetryExecption($e, [
                'method' => $method,
                'params' => $requestParams,
            ]);
        }

        throw new FailedRequestExecption($e, [
            'method' => $method,
            'params' => $requestParams,
        ]);
    }

    /**
     * @param \Exception $e
     *
     * @return bool
     */
    private function isMaximumRetryError(\Exception $e)
    {
        return strpos($e->getMessage(), 'Maximum number of retry attempts') !== false;
    }

    /**
     * エラーハンドリング(getUserInfo用)
     *
     * @param \Exception $e
     * @param string $method
     *
     * @return void
     */
    public function handleGetUserInfoError(\Exception $e, string $method)
    {
        if ($this->isAuthenticationError($e)) {
            throw new GetUserInfoInvalidAccessTokenException(__('error.amazon_pay_invalid_token', [
                'method' => $method,
            ]));
        }

        throw new FailedRequestExecption($e, [
            'method' => $method,
            'params' => null,
        ]);
    }

    /**
     * トークン検証エラーの判定（getUserInfoのみ）
     *
     * @param \Exception $e
     *
     * @return bool
     */
    private function isAuthenticationError(\Exception $e)
    {
        return strpos($e->getMessage(), 'The Access Token belongs to neither your Client ID nor App ID') !== false;
    }
}
