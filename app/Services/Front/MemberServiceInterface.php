<?php

namespace App\Services\Front;

interface MemberServiceInterface
{
    /**
     * 会員仮登録
     *
     * @param array $params
     *
     * @return array
     */
    public function storeTemp(array $params);

    /**
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function updateMemberToken(int $memberId, array $params);

    /**
     * 会員本登録
     *
     * @param int $memberId
     * @param array $params
     * @param string|null $memberToken
     *
     * @return array
     */
    public function update(int $memgerId, array $params, ?string $memberToken = null);

    /**
     * 会員詳細
     *
     * @param int $memberId
     * @param string $memberToken
     *
     * @return array
     */
    public function get(int $memgerId, string $memberToken = null);

    /**
     * パスワードリセット
     *
     * @param array $params
     *
     * @return array
     */
    public function sendPasswordResetRequest(array $params);

    /**
     * パスワードリセット後の新パスワード設定
     *
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function resetPasswordDecision(int $memberId, array $params);

    /**
     * メールアドレス変更
     *
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function changeEmail(int $memberId, array $params);

    /**
     * パスワード変更
     *
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function changePassword(int $memberId, array $params);

    /**
     * 会員発行可能クーポン一覧取得
     *
     * @param array $params
     *
     * @return array
     */
    public function getCoupons($memberId, array $params);

    /**
     * 会員クーポン発行
     *
     * @param array $params
     *
     * @return array
     */
    public function issueCoupon($memberId, int $couponId, array $params);

    /**
     * 会員利用可能クーポン一覧取得
     *
     * @param array $params
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAvailableCoupons($memberId, array $params = []);

    /**
     * 会員クーポン利用
     *
     * @param array $params
     *
     * @return array
     */
    public function useCoupon($memberId, int $couponId, array $params);

    /**
     * 退会
     *
     * @param int $memberId
     * @param array $params
     *
     * @return bool
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
    public function getPointHistory(int $memberId, array $params);
}
