<?php

namespace App\HttpCommunication\Ymdy;

use App\HttpCommunication\HttpCommunicationServiceInterface;

interface DestinationInterface extends HttpCommunicationServiceInterface
{
    const ENDPOINT_INDEX_SHIPPING_ADDRESS = 'index_shipping_address';
    const ENDPOINT_STORE_SHIPPING_ADDRESS = 'store_shipping_address';
    const ENDPOINT_SHOW_SHIPPING_ADDRESS = 'show_shipping_address';
    const ENDPOINT_UPDATE_SHIPPING_ADDRESS = 'update_shipping_address';
    const ENDPOINT_DELETE_SHIPPING_ADDRESS = 'delete_shipping_address';

    /**
     * 会員配送先住所一覧
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function indexDestinations(int $memberId, array $params);

    /**
     * 会員配送先住所登録
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function storeDestination(int $memberId, array $params);

    /**
     * 会員配送先住所詳細
     *
     * @param int $destinationId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function showDestination(int $destinationId);

    /**
     * 会員配送先住所更新
     *
     * @param int $destinationId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function updateDestination(int $destinationId, array $params);

    /**
     * 会員配送先住所削除
     *
     * @param int $destinationId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function deleteDestination(int $destinationId);
}
