<?php

namespace App\HttpCommunication\Ymdy;

use App\HttpCommunication\HttpCommunicationServiceInterface;

interface MemberInterface extends HttpCommunicationServiceInterface
{
    const ENDPONT_INDEX_MEMBER = 'index_member';
    const ENDPONT_SHOW_MEMBER = 'show_member';
    const ENDPONT_INDEX_AVAILABLE_COUPON = 'index_available_coupon';
    const ENDPONT_USE_AVAILABLE_COUPON = 'use_available_coupon';
    const ENDPONT_ADD_POINT_TO_MEMBER = 'add_point_to_member';

    const ENDPOINT_AUTH_PASSWORD = 'auth_password';
    const ENDPOINT_STORE_TEMP = 'store_temp';
    const ENDPOINT_STORE_AMAZON = 'store_amazon';
    const ENDPOINT_UPDATE_MEMBER = 'update_member';
    const ENDPOINT_LINK_AMAZON = 'link_amazon';
    const ENDPOINT_TOKEN_REFRESH = 'token_refresh';
    const ENDPOINT_TOKEN_EXPIRE = 'token_expire';
    const ENDPOINT_GET_MEMBERS = 'index_member';
    const ENDPOINT_GET_MEMBER_DETAIL = 'show_member';
    const ENDPOINT_RESET_PASSWORD = 'reset_password';
    const ENDPOINT_RESET_PASSWORD_DECISION = 'reset_password_decision';
    const ENDPOINT_CHANGE_EMAIL = 'change_email';
    const ENDPOINT_CHANGE_PASSWORD = 'change_password';
    const ENDPOINT_MEMBER_COUPONS = 'index_member_coupon';
    const ENDPOINT_MEMBER_AVAILABLE_COUPONS = 'index_member_available_coupon';
    const ENDPOINT_SEARCH_MEMBER_AVAILABLE_COUPON = 'search_member_available_coupon';
    const ENDPOINT_CHECK_MEMBER_AVAILABLE_COUPON = 'check_member_available_coupon';
    const ENDPOINT_SHOW_COUPON = 'show_coupon';
    const ENDPOINT_ISSUE_MEMBER_COUPON = 'issue_member_coupon';
    const ENDPOINT_USE_MEMBER_AVAILABLE_COUPON = 'use_member_available_coupon';
    const ENDPOINT_STORE_PURCHASE = 'store_purchase';
    const ENDPOINT_UPDATE_PURCHASE = 'update_purchase';
    const ENDPOINT_WITHDRAW = 'withdraw';
    const ENDPOINT_POINT_HISTORY = 'point_history';
    const ENDPOINT_AUTH_AGENT = 'auth_agent';

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
     * 会員一覧・検索
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function indexMember(array $query = []);

    /**
     * 会員詳細
     *
     * @param int $memberId
     * @param string $memberToken
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showMember(int $memberId, string $memberToken = null);

    /**
     * クーポン利用
     *
     * @param int $memberId
     * @param int $couponId
     * @param string $memberToken
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function useAvailableCoupon(int $memberId, int $couponId, string $memberToken);

    /**
     * ポイント付与
     *
     * @param array $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function addPointToMember(array $body);

    /**
     * パスワード認証
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authPassword(array $params);

    /**
     * 会員仮登録
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function storeTemp(array $params);

    /**
     * 会員本登録・更新
     *
     * @param int $memberId
     * @param array $params
     * @param string $memberToken
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function updateMember(int $memberId, array $params, string $memberToken = null);

    /**
     * 会員Amazon登録
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function storeAmazon(array $params);

    /**
     * 会員amazonアカウント紐付け
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function linkAmazon(int $memberId, array $params);

    /**
     * トークンリフレッシュ
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function tokenRefresh(array $params);

    /**
     * トークン破棄
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function tokenExpire();

    /**
     * 会員パスワード再設定依頼
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function resetPassword(array $params);

    /**
     * 会員パスワード再設定
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function resetPasswordDecision(int $memberId, array $params);

    /**
     * 会員検索
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchMembers();

    /**
     * 会員詳細
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchMemberDetail(int $memberId);

    /**
     * メールアドレス変更
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function changeEmail(int $memberId, array $params);

    /**
     * パスワード変更
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function changePassword(int $memberId, array $params);

    /**
     * 会員発行可能クーポン一覧取得
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getCoupons(int $memberId, array $params);

    /**
     * 会員クーポン発行
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function issueCoupon(int $memberId, int $couponId, array $params);

    /**
     * 会員利用可能クーポン一覧取得
     *
     * @param int $memberId
     * @param array $query
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getAvailableCoupons(int $memberId, ?array $query = []);

    /**
     * 会員利用可能クーポン検索
     *
     * @param int $memberId
     * @param array|null $query
     * @param array|null $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function searchAvailableCoupon(int $memberId, ?array $query = [], ?array $body = []);

    /**
     * クーポン併用可能の判定
     *
     * @param int $memberId
     * @param array $body
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function checkAvailableCoupons(int $memberId, array $body = []);

    /**
     * 会員クーポン利用
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function useCoupon(int $memberId, int $couponId, array $params);

    /**
     * クーポン詳細取得
     *
     * @param int $couponId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showCoupon($couponId);

    /**
     * 会員購買登録
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function storePurchase(int $memberId, array $params);

    /**
     * 会員購買登録
     *
     * @param int $memberId
     * @param string $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function updatePurchase(int $memberId, string $purchaseId, array $params);

    /**
     * 会員削除
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function withdraw(int $memberId, array $params);

    /**
     * 会員ポイント履歴取得
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function pointHistory(int $memberId, array $params);

    /**
     * 代理ログイン
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authAgent(array $params);
}
