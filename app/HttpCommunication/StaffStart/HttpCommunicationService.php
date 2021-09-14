<?php

namespace App\HttpCommunication\StaffStart;

use App\HttpCommunication\Exceptions\StaffStartHttpException;
use App\HttpCommunication\HttpCommunicationService as BaseHttpCommunicationService;
use App\HttpCommunication\Response\Concrete\StaffStartResponse;
use App\HttpCommunication\StaffStart\Execptions\FailedCodeException;

abstract class HttpCommunicationService extends BaseHttpCommunicationService
{
    const CODE_SUCCESS = 1;
    const CODE_ERROR = -1;

    const MAX_REDIRECT = 5;

    public function getConfigKey(): string
    {
        return 'staff_start';
    }

    /**
     * レスポンスボディの配列から成功・失敗を判定
     *
     * @return bool
     */
    public function isSuccess(array $body)
    {
        return !isset($body['code']) || (int) $body['code'] === self::CODE_SUCCESS;
    }

    /**
     * リクエスト送信
     * NOTE: GuzzleHttpだと403が返ってくるため暫定的にCURLで対応する。
     *       そのため、$clientプロパティは使用されない
     *
     * @param string $endpointKey
     * @param array $params
     * @param array $body
     * @param array $options クエリパラメータもここに入れる (例: [ 'query' => ['abc' => 'dfg'] ]）
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function request(string $endpointKey, array $params = [], array $body = [], array $options = [])
    {
        if (!isset($options['query'])) {
            $options['query'] = [];
        }

        $params = $this->preprocessRequest([$endpointKey, $params, $body, $options]);

        [$uri, $headers, $body, $options] = array_slice($params, 1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, self::MAX_REDIRECT);
        curl_setopt($ch, CURLOPT_URL, $uri . '?' . http_build_query($options['query']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $rawResponse = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = new StaffStartResponse($rawResponse, $status);

        if ($response->getStatusCode() >= 400) {
            throw new StaffStartHttpException($response);
        }

        if (!$this->isSuccess($body = $response->getBody())) {
            throw new FailedCodeException(error_format('error.staff_code_failed_code', $body));
        }

        return $response;
    }

    /**
     * @param array $bundle
     *
     * @return array
     */
    protected function preprocessRequest(array $bundle)
    {
        $params = parent::preprocessRequest($bundle);

        [$method, $uri, $headers, $body, $options] = $params;

        $options['query'] = array_merge(['merchant_id' => $this->getConfig('merchant_id')], $options['query']);

        $headers = $this->convertHeaders($headers);

        return [$method, $uri, $headers, $body, $options];
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    private function convertHeaders(array $headers)
    {
        $convertedHeaders = [];

        foreach ($headers as $key => $value) {
            $convertedHeaders[] = $key . ': ' . $value;
        }

        return $convertedHeaders;
    }
}
