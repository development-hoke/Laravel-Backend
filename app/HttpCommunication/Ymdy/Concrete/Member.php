<?php

namespace App\HttpCommunication\Ymdy\Concrete;

use App\HttpCommunication\Ymdy\HttpCommunicationService;
use App\HttpCommunication\Ymdy\MemberInterface;

/**
 * 会員ポイントシステム
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Member extends HttpCommunicationService implements MemberInterface
{
    const DEFAULT_AVAILABLE_COUPON_PER_PAGE = 1000;

    /**
     * トークンが必要なエンドポイントの設定
     *
     * @var array
     */
    protected $needsToken = [
        self::ENDPOINT_GET_MEMBER_DETAIL,
        self::ENDPOINT_MEMBER_AVAILABLE_COUPONS,
        self::ENDPOINT_SEARCH_MEMBER_AVAILABLE_COUPON,
        self::ENDPONT_SHOW_MEMBER,
        self::ENDPOINT_UPDATE_MEMBER,
        self::ENDPOINT_POINT_HISTORY,
        self::ENDPOINT_MEMBER_COUPONS,
        self::ENDPOINT_CHANGE_EMAIL,
        self::ENDPOINT_CHANGE_PASSWORD,
        self::ENDPOINT_WITHDRAW,
        self::ENDPOINT_TOKEN_REFRESH,
        self::ENDPOINT_ISSUE_MEMBER_COUPON,
        self::ENDPOINT_STORE_PURCHASE,
        self::ENDPOINT_UPDATE_PURCHASE,
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
     * 会員一覧・検索
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function indexMember(array $query = [])
    {
        return $this->request(self::ENDPONT_INDEX_MEMBER, [], [], ['query' => $query]);
    }

    /**
     * 会員詳細
     *
     * @param int $memberId
     * @param string $memberToken
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showMember(int $memberId, string $memberToken = null)
    {
        if (isset($memberToken)) {
            return $this->request(self::ENDPONT_SHOW_MEMBER, ['member_id' => $memberId], [], [
                'headers' => [
                    'Member-Token' => $memberToken,
                ],
            ]);
        }

        return $this->request(self::ENDPONT_SHOW_MEMBER, ['member_id' => $memberId]);
    }

    /**
     * クーポン利用
     *
     * @param int $memberId
     * @param int $couponId
     * @param string $memberToken
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function useAvailableCoupon(int $memberId, int $couponId, string $memberToken)
    {
        $params = ['member_id' => $memberId, 'coupon_id' => $couponId];

        return $this->request(self::ENDPONT_USE_AVAILABLE_COUPON, $params);
    }

    /**
     * ポイント付与
     *
     * @param array $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function addPointToMember(array $body)
    {
        return $this->request(self::ENDPONT_ADD_POINT_TO_MEMBER, [], $body);
    }

    /**
     * パスワード認証
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function authPassword(array $params)
    {
        return $this->request(self::ENDPOINT_AUTH_PASSWORD, [], $params);
    }

    /**
     * 会員仮登録
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function storeTemp(array $params)
    {
        return $this->request(self::ENDPOINT_STORE_TEMP, [], $params);
    }

    /**
     * 会員本登録・更新
     *
     * @param int $memberId
     * @param array $params
     * @param string $memberToken
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function updateMember(int $memberId, array $params, string $memberToken = null)
    {
        if (isset($memberToken)) {
            return $this->request(self::ENDPOINT_UPDATE_MEMBER, ['member_id' => $memberId], $params, [
                'headers' => [
                    'Member-Token' => $memberToken,
                ],
            ]);
        }

        return $this->request(self::ENDPOINT_UPDATE_MEMBER, ['member_id' => $memberId], $params);
    }

    /**
     * 会員Amazon登録
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function storeAmazon(array $params)
    {
        return $this->request(self::ENDPOINT_STORE_AMAZON, [], $params);
    }

    /**
     * 会員amazonアカウント紐付け
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function linkAmazon(int $memberId, array $params)
    {
        return $this->request(self::ENDPOINT_LINK_AMAZON, ['member_id' => $memberId], $params);
    }

    /**
     * トークンリフレッシュ
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function tokenRefresh(array $params)
    {
        return $this->request(self::ENDPOINT_TOKEN_REFRESH, [], $params);
    }

    /**
     * トークン破棄
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function tokenExpire()
    {
        return $this->request(self::ENDPOINT_TOKEN_EXPIRE);
    }

    /**
     * 会員パスワード再設定依頼
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function resetPassword(array $params)
    {
        return $this->request(self::ENDPOINT_RESET_PASSWORD, [], $params);
    }

    /**
     * 会員パスワード再設定
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function resetPasswordDecision(int $memberId, array $params)
    {
        if (isset($params['member_token'])) {
            return $this->request(self::ENDPOINT_RESET_PASSWORD_DECISION, ['member_id' => $memberId], $params, [
                'headers' => [
                    'Member-Token' => $params['member_token'],
                ],
            ]);
        }

        return $this->request(self::ENDPOINT_RESET_PASSWORD_DECISION, ['member_id' => $memberId], $params);
    }

    /**
     * 会員検索
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchMembers()
    {
        return $this->request(self::ENDPOINT_GET_MEMBERS);
    }

    /**
     * 会員詳細
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchMemberDetail(int $memberId)
    {
        return $this->request(self::ENDPOINT_GET_MEMBER_DETAIL, ['member_id' => $memberId]);
    }

    /**
     * メールアドレス変更
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function changeEmail(int $memberId, array $params)
    {
        return $this->request(self::ENDPOINT_CHANGE_EMAIL, ['member_id' => $memberId], $params);
    }

    /**
     * パスワード変更
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function changePassword(int $memberId, array $params)
    {
        return $this->request(self::ENDPOINT_CHANGE_PASSWORD, ['member_id' => $memberId], $params);
    }

    /**
     * 会員発行可能クーポン一覧取得
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getCoupons(int $memberId, array $params)
    {
        return $this->request(self::ENDPOINT_MEMBER_COUPONS, ['member_id' => $memberId], $params);
    }

    /**
     * 会員クーポン発行
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function issueCoupon(int $memberId, int $couponId, array $params)
    {
        return $this->request(self::ENDPOINT_ISSUE_MEMBER_COUPON, [
            'member_id' => $memberId,
            'coupon_id' => $couponId,
        ], $params);
    }

    /**
     * 会員利用可能クーポン一覧取得
     *
     * @param int $memberId
     * @param array $query
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getAvailableCoupons(int $memberId, ?array $query = [])
    {
        // NOTE: 会員一人あたりの利用可能件数は多くならない想定なので、1000件を指定して、
        // 全件取得されるという前提で実装する
        $query = array_merge([
            'per_page' => self::DEFAULT_AVAILABLE_COUPON_PER_PAGE,
        ], $query);

        return $this->request(self::ENDPOINT_MEMBER_AVAILABLE_COUPONS, ['member_id' => $memberId], [], ['query' => $query]);
    }

    /**
     * 会員利用可能クーポン検索
     *
     * @param int $memberId
     * @param array|null $query
     * @param array|null $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function searchAvailableCoupon(int $memberId, ?array $query = [], ?array $body = [])
    {
        return $this->request(self::ENDPOINT_SEARCH_MEMBER_AVAILABLE_COUPON, ['member_id' => $memberId], $body, ['query' => $query]);
    }

    /**
     * クーポン併用可能の判定
     *
     * @param int $memberId
     * @param array $body
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function checkAvailableCoupons(int $memberId, array $body = [])
    {
        return $this->request(self::ENDPOINT_CHECK_MEMBER_AVAILABLE_COUPON, ['member_id' => $memberId], $body);
    }

    /**
     * 会員クーポン利用
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function useCoupon(int $memberId, int $couponId, array $params)
    {
        return $this->request(self::ENDPOINT_USE_MEMBER_AVAILABLE_COUPON, [
            'member_id' => $memberId,
            'coupon_id' => $couponId,
        ], $params);
    }

    /**
     * クーポン詳細取得
     *
     * @param int $couponId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showCoupon($couponId)
    {
        $params = ['coupon_id' => $couponId];

        return $this->request(self::ENDPOINT_SHOW_COUPON, $params);
    }

    /**
     * 会員購買登録
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\Concrete\Response|\App\HttpCommunication\Response\ResponseInterface
     */
    public function storePurchase(int $memberId, array $params)
    {
        return $this->request(self::ENDPOINT_STORE_PURCHASE, [
            'member_id' => $memberId,
        ], $params);
    }

    /**
     * 会員購買登録
     *
     * @param int $memberId
     * @param string $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function updatePurchase(int $memberId, string $purchaseId, array $params)
    {
        return $this->request(self::ENDPOINT_UPDATE_PURCHASE, [
            'member_id' => $memberId,
            'purchase_id' => $purchaseId,
        ], $params);
    }

    /**
     * 会員削除
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\Concrete\Response|\App\HttpCommunication\Response\ResponseInterface
     */
    public function withdraw(int $memberId, array $params)
    {
        return $this->request(self::ENDPOINT_WITHDRAW, ['member_id' => $memberId], $params);
    }

    /**
     * 会員ポイント履歴取得
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function pointHistory(int $memberId, array $params)
    {
        return $this->request(self::ENDPOINT_POINT_HISTORY, ['member_id' => $memberId], [], $params);
    }

    /**
     * 代理ログイン
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authAgent(array $params)
    {
        return $this->request(self::ENDPOINT_AUTH_AGENT, [], $params);
    }
}
