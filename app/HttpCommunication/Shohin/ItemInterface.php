<?php

namespace App\HttpCommunication\Shohin;

use App\HttpCommunication\Response\ResponseInterface;

interface ItemInterface
{
    const ENDPOINT_FETCH_STOCKS = 'fetch_stocks';
    const ENDPOINT_FETCH_MASTERS = 'fetch_masters';
    const ENDPOINT_PURCHASE = 'purchase';
    const ENDPOINT_PURCHASE_CANCEL = 'purchase_cancel';
    const ENDPOINT_EC_UPDATE = 'ec_update';

    /**
     * 在庫情報取得
     *
     * @param array $params
     *
     * @return ResponseInterface
     */
    public function fetchStocks(array $params);

    /**
     * 商品情報取得
     *
     * @param array $params
     *
     * @return ResponseInterface
     */
    public function fetchMasters(array $params);

    /**
     * 販売情報登録
     *
     * @param array $body
     *
     * @return ResponseInterface
     */
    public function purchase(array $body);

    /**
     * 注文キャンセル
     *
     * @param string $code
     *
     * @return ResponseInterface
     */
    public function purchaseCancel(string $code);

    /**
     * EC情報変更
     *
     * @param array $body
     *
     * @return ResponseInterface
     */
    public function ecUpdate(array $body);
}
