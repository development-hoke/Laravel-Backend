<?php

namespace App\HttpCommunication\Ymdy\Mock;

use App\HttpCommunication\Response\Mock\Response;
use App\HttpCommunication\Response\ResponseInterface;
use App\HttpCommunication\Ymdy\HttpCommunicationService;
use App\HttpCommunication\Ymdy\MemberShippingAddressInterface;

/*
 * 会員・ポイントシステムとの連携で使用する（モック）
 * 会員ポイントシステム
 */

class MemberShippingAddress extends HttpCommunicationService implements MemberShippingAddressInterface
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
     * エンドポイント設定 prefix取得
     *
     * @return null
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
        return $this;
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
        return $this;
    }

    /**
     * 会員配送先住所一覧
     *
     * @param int $memberId
     * @param array $query
     *
     * @return Response
     */
    public function index(int $memberId, array $query = [])
    {
        return new Response([
            'shipping_addresses' => [
                [
                    'id' => 1,
                    'member_id' => $memberId,
                    'lname' => '田中',
                    'fname' => '太郎',
                    'lkana' => 'タナカ',
                    'fkana' => 'タロウ',
                    'tel' => '0300000000',
                    'pref_id' => 13,
                    'pref' => [
                        'id' => 13,
                        'name' => '東京都',
                        'created_at' => '2020-01-01 09:00:00',
                        'updated_at' => '2020-01-01 09:00:00',
                    ],
                    'zip' => '1638001',
                    'city' => '中央区',
                    'town' => '東日本橋',
                    'address' => '1-6-9',
                    'building' => 'グリーンパーク東日本橋２ ２０１',
                ],
                [
                    'id' => 2,
                    'member_id' => $memberId,
                    'lname' => '田中',
                    'fname' => '太郎',
                    'lkana' => 'タナカ',
                    'fkana' => 'タロウ',
                    'tel' => '0300000000',
                    'pref_id' => 13,
                    'pref' => [
                        'id' => 13,
                        'name' => '東京都',
                        'created_at' => '2020-01-01 09:00:00',
                        'updated_at' => '2020-01-01 09:00:00',
                    ],
                    'zip' => '1638001',
                    'city' => '中央区',
                    'town' => '東日本橋',
                    'address' => '1-6-9',
                    'building' => 'グリーンパーク東日本橋２ ２０１',
                ],
            ],
        ]);
    }

    /**
     * 会員配送先住所登録
     *
     * @param int $memberId
     * @param array $body
     *
     * @return Response
     */
    public function store(int $memberId, array $body)
    {
        return new Response([
            'shipping_address' => [
                'id' => 1,
                'member_id' => $memberId,
                'lname' => '田中',
                'fname' => '太郎',
                'lkana' => 'タナカ',
                'fkana' => 'タロウ',
                'tel' => '0300000000',
                'pref_id' => 13,
                'pref' => [
                    'id' => 13,
                    'name' => '東京都',
                    'created_at' => '2020-01-01 09:00:00',
                    'updated_at' => '2020-01-01 09:00:00',
                ],
                'zip' => '1638001',
                'city' => '中央区',
                'town' => '東日本橋',
                'address' => '1-6-9',
                'building' => 'グリーンパーク東日本橋２ ２０１',
            ],
        ]);
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
        return new Response([
            'shipping_address' => [
                'id' => 1,
                'member_id' => 1,
                'lname' => '田中',
                'fname' => '太郎',
                'lkana' => 'タナカ',
                'fkana' => 'タロウ',
                'tel' => '0300000000',
                'pref_id' => 13,
                'pref' => [
                    'id' => 13,
                    'name' => '東京都',
                    'created_at' => '2020-01-01 09:00:00',
                    'updated_at' => '2020-01-01 09:00:00',
                ],
                'zip' => '1638001',
                'city' => '中央区',
                'town' => '東日本橋',
                'address' => '1-6-9',
                'building' => 'グリーンパーク東日本橋２ ２０１',
            ],
        ]);
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
        return new Response([
            'shipping_address' => [
                'id' => 1,
                'member_id' => 1,
                'lname' => '田中',
                'fname' => '太郎',
                'lkana' => 'タナカ',
                'fkana' => 'タロウ',
                'tel' => '0300000000',
                'pref_id' => 13,
                'pref' => [
                    'id' => 13,
                    'name' => '東京都',
                    'created_at' => '2020-01-01 09:00:00',
                    'updated_at' => '2020-01-01 09:00:00',
                ],
                'zip' => '1638001',
                'city' => '中央区',
                'town' => '東日本橋',
                'address' => '1-6-9',
                'building' => 'グリーンパーク東日本橋２ ２０１',
            ],
        ]);
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
        return new Response();
    }
}
