<?php

namespace App\HttpCommunication\Ymdy;

use App\HttpCommunication\HttpCommunicationServiceInterface;
use App\HttpCommunication\Response\ResponseInterface;

interface MemberShippingAddressInterface extends HttpCommunicationServiceInterface
{
    const ENDPOINT_PREFIX = 'shipping_address';

    const ENDPOINT_INDEX = 'index';
    const ENDPOINT_STORE = 'store';
    const ENDPOINT_GET = 'get';
    const ENDPOINT_UPDATE = 'update';
    const ENDPOINT_DESTROY = 'destroy';

    const HEADER_MEMBER_TOKEN = 'Member-Token';
    const HEADER_STAFF_TOKEN = 'Staff-Token';

    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberTokenHeader(string $token);

    /**
     * スタッフトークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token);

    /**
     * 会員配送先住所一覧
     *
     * @param int $memberId
     * @param array $query
     *
     * @return ResponseInterface
     */
    public function index(int $memberId, array $query = []);

    /**
     * 会員配送先住所登録
     *
     * @param int $memberId
     * @param array $body
     *
     * @return ResponseInterface
     */
    public function store(int $memberId, array $body);

    /**
     * 会員配送先住所詳細
     *
     * @param int $addressId
     *
     * @return ResponseInterface
     */
    public function get(int $addressId);

    /**
     * 会員配送先住所更新
     *
     * @param array $body
     * @param int $addressId
     *
     * @return ResponseInterface
     */
    public function update(int $addressId, array $body);

    /**
     * 会員配送先住所削除
     *
     * @param int $addressId
     *
     * @return ResponseInterface
     */
    public function destroy(int $addressId);
}
