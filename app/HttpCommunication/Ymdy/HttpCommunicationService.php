<?php

namespace App\HttpCommunication\Ymdy;

use App\HttpCommunication\HttpCommunicationService as BaseHttpCommunicationService;
use App\HttpCommunication\Ymdy\Traits\HasTokenHeader;

abstract class HttpCommunicationService extends BaseHttpCommunicationService
{
    use HasTokenHeader;

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
    public function request(
        string $endpointKey,
        array $params = [],
        array $body = [],
        array $options = []
    ) {
        // ヘッダにトークンが必要な場合ここで代入する。
        $options = $this->mergeTokenHeaderToOptions($options, $endpointKey);

        return parent::request($endpointKey, $params, $body, $options);
    }
}
