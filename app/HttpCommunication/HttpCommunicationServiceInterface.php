<?php

namespace App\HttpCommunication;

interface HttpCommunicationServiceInterface
{
    /**
     * 追加で指定するヘッダの設定。次回のリクエストのみに適用される。
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function setAdditionalHeader(string $name, $value);

    /**
     * 追加で指定するヘッダの削除。
     *
     * @param string $name
     *
     * @return void
     */
    public function removeAdditionalHeader(string $name);

    /**
     * @param string|null $key
     *
     * @return array|mixed
     */
    public function getConfig(string $key = null);

    /**
     * @param string $key
     * @param array $params
     *
     * @return array
     */
    public function getEndpoint(string $key, array $params = []);

    /**
     * @param int $maxRetryTimes
     *
     * @return void
     */
    public function setMaxRetryTimes(int $maxRetryTimes);

    /**
     * リクエスト送信
     *
     * @param string $endpointKey
     * @param array $params
     * @param array $body
     * @param array $options クエリパラメータもここに入れる (例: [ 'query' => ['abc' => 'dfg'] ]）
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function request(string $endpointKey, array $params = [], array $body = [], array $options = []);
}
