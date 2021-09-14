<?php

namespace App\HttpCommunication\Shohin\Concrete;

use App\HttpCommunication\Shohin\HttpCommunicationService;
use App\HttpCommunication\Shohin\ItemInterface;

class Item extends HttpCommunicationService implements ItemInterface
{
    /**
     * configを取得するためのキー
     *
     * @return string
     */
    protected function getConfigKey(): string
    {
        return 'shohin';
    }

    /**
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\Concrete\Response|\App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchStocks(array $params)
    {
        return $this->request(self::ENDPOINT_FETCH_STOCKS, [], $params);
    }

    /**
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\Concrete\Response|\App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchMasters(array $params)
    {
        return $this->request(self::ENDPOINT_FETCH_MASTERS, [], $params);
    }

    /**
     * @param array $body
     *
     * @return \App\HttpCommunication\Response\Concrete\Response|\App\HttpCommunication\Response\ResponseInterface
     */
    public function purchase(array $body)
    {
        return $this->request(self::ENDPOINT_PURCHASE, [], $body);
    }

    /**
     * 注文キャンセル
     *
     * @param string $code
     *
     * @return ResponseInterface
     */
    public function purchaseCancel(string $code)
    {
        return $this->request(self::ENDPOINT_PURCHASE_CANCEL, [], ['order_id' => $code]);
    }

    /**
     * EC情報変更
     *
     * @param array $body
     *
     * @return ResponseInterface
     */
    public function ecUpdate(array $body)
    {
        return $this->request(self::ENDPOINT_EC_UPDATE, [], $body);
    }
}
