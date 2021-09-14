<?php

namespace App\HttpCommunication\AmazonPay\Exceptions;

use App\HttpCommunication\Response\ResponseInterface;

/**
 * HTTPレスポンでエラーが返ってきたときの例外
 *
 * @see http://docs.developer.amazonservices.com/ja_JP/dev_guide/DG_ResponseFormat.html
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/error-codes.html
 */
class HttpException extends Exception
{
    /**
     * @param ResponseInterface $response
     */
    private $response;

    /**
     * @var string
     */
    private $amazonErrorCode;

    /**
     * @var string
     */
    private $originalMessage;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;

        $info = $this->parseBody($response->getBody());

        $this->amazonErrorCode = $info['code'];
        $this->originalMessage = $info['message'];

        parent::__construct(sprintf('[AMAZON_PAY] CODE: %s MESSAGE: %s', $info['code'], $info['message']), $response->getStatusCode());
    }

    /**
     * @param array $body
     *
     * @return static
     */
    private function parseBody(array $body)
    {
        return [
            'code' => $body['Error']['Code'] ?? \App\Enums\AmazonPay\ErrorCode::UnparsableErrorResponse,
            'message' => $body['Error']['Message'] ?? '',
        ];
    }

    /**
     * @return string
     */
    public function getOriginalMessage()
    {
        return $this->originalMessage;
    }

    /**
     * @return string
     */
    public function getAmazonErrorCode()
    {
        return $this->amazonErrorCode;
    }

    /**
     * @return array
     */
    public function getResponseBody()
    {
        return $this->response->getBody();
    }

    /**
     * @return int
     */
    public function getResponseStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
