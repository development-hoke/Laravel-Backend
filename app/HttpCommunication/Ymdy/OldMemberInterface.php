<?php

namespace App\HttpCommunication\Ymdy;

interface OldMemberInterface
{
    const ENDPOINT_PIN = 'pin';
    const ENDPOINT_CHECK_MAIL = 'check_mail';
    const ENDPOINT_CONTACT = 'old_member_contact';
    const ENDPOINT_FORGET_MAIL_SEND = 'old_member_forget_mail_send';
    const ENDPOINT_CARD_MAIL = 'card_mail_auth';

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
     * 旧カード会員PINコード認証
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function pin(array $params);

    /**
     * 旧会員メールアドレス認証メール送信
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function checkMail(array $params);

    /**
     * 旧会員カスタマーサービス連絡
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function contact(array $params);

    /**
     * 旧会員メールアドレス忘れメールアドレス認証メール送信
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function forgetMail(array $params);
}
