<?php

namespace App\HttpCommunication\Ymdy\Concrete;

use App\HttpCommunication\Ymdy\DestinationInterface;
use App\HttpCommunication\Ymdy\HttpCommunicationService;

/**
 * 会員ポイントシステム
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Destination extends HttpCommunicationService implements DestinationInterface
{
    /**
     * configを取得するためのキー
     *
     * @return string
     */
    protected function getConfigKey(): string
    {
        return 'ymdy_member';
    }

    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberTokenHeader(string $token)
    {
        return $this->setAdditionalHeader(static::HEADER_MEMBER_TOKEN, $token);
    }

    /**
     * スタッフトークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token)
    {
        return $this->setAdditionalHeader(static::HEADER_STAFF_TOKEN, $token);
    }

    /**
     * 会員配送先住所一覧
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function indexDestinations(int $memberId, array $params)
    {
        return $this->request(self::ENDPOINT_INDEX_SHIPPING_ADDRESS, ['member_id' => $memberId], $params);
    }

    /**
     * 会員配送先住所登録
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function storeDestination(int $memberId, array $params)
    {
        return $this->request(self::ENDPOINT_STORE_SHIPPING_ADDRESS, ['member_id' => $memberId], $params);
    }

    /**
     * 会員配送先住所詳細
     *
     * @param int $destinationId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function showDestination(int $destinationId)
    {
        return $this->request(self::ENDPOINT_SHOW_SHIPPING_ADDRESS, ['shipping_address_id' => $destinationId]);
    }

    /**
     * 会員配送先住所更新
     *
     * @param int $destinationId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function updateDestination(int $destinationId, array $params)
    {
        return $this->request(self::ENDPOINT_UPDATE_SHIPPING_ADDRESS, ['shipping_address_id' => $destinationId], $params);
    }

    /**
     * 会員配送先住所削除
     *
     * @param int $destinationId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function deleteDestination(int $destinationId)
    {
        return $this->request(self::ENDPOINT_DELETE_SHIPPING_ADDRESS, ['shipping_address_id' => $destinationId]);
    }
}
