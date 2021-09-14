<?php

namespace App\HttpCommunication\Ymdy\Concrete;

use App\HttpCommunication\Response\ResponseInterface;
use App\HttpCommunication\Ymdy\HttpCommunicationService;
use App\HttpCommunication\Ymdy\MemberShippingAddressInterface;

/*
 * 会員・ポイントシステムとの連携で使用する
 * 会員ポイントシステム
 */

class MemberShippingAddress extends HttpCommunicationService implements MemberShippingAddressInterface
{
    /**
     * トークンが必要なエンドポイントの設定
     *
     * @var array
     */
    protected $needsToken = [
        self::ENDPOINT_INDEX,
        self::ENDPOINT_STORE,
        self::ENDPOINT_GET,
        self::ENDPOINT_UPDATE,
        self::ENDPOINT_DESTROY,
    ];

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
     * エンドポイント設定 prefix取得
     *
     * @return string
     */
    protected function getEndpointPrefix()
    {
        return 'shipping_address';
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
        return $this->setTokenHeader(static::HEADER_MEMBER_TOKEN, $token);
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
        return $this->setTokenHeader(static::HEADER_STAFF_TOKEN, $token);
    }

    /**
     * 会員配送先住所一覧
     *
     * @param int $memberId
     * @param array $query
     *
     * @return ResponseInterface
     */
    public function index(int $memberId, array $query = [])
    {
        return $this->request(self::ENDPOINT_INDEX, ['member_id' => $memberId], [], ['query' => $query]);
    }

    /**
     * 会員配送先住所登録
     *
     * @param int $memberId
     * @param array $body
     *
     * @return ResponseInterface
     */
    public function store(int $memberId, array $body)
    {
        return $this->request(self::ENDPOINT_STORE, ['member_id' => $memberId], $body);
    }

    /**
     * 会員配送先住所詳細
     *
     * @param int $addressId
     *
     * @return ResponseInterface
     */
    public function get(int $addressId)
    {
        $pathParam = [
            'shipping_address_id' => $addressId,
        ];

        return $this->request(self::ENDPOINT_GET, $pathParam);
    }

    /**
     * 会員配送先住所更新
     *
     * @param array $body
     * @param int $addressId
     *
     * @return ResponseInterface
     */
    public function update(int $addressId, array $body)
    {
        $pathParam = [
            'shipping_address_id' => $addressId,
        ];

        return $this->request(self::ENDPOINT_UPDATE, $pathParam, $body);
    }

    /**
     * 会員配送先住所削除
     *
     * @param int $addressId
     *
     * @return ResponseInterface
     */
    public function destroy(int $addressId)
    {
        $pathParam = [
            'shipping_address_id' => $addressId,
        ];

        return $this->request(self::ENDPOINT_DESTROY, $pathParam);
    }
}
