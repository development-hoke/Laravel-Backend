<?php

namespace App\HttpCommunication\Ymdy\Concrete;

use App\HttpCommunication\Ymdy\HttpCommunicationService;
use App\HttpCommunication\Ymdy\OldMemberInterface;

/**
 * 会員ポイントシステム
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OldMember extends HttpCommunicationService implements OldMemberInterface
{
    /**
     * トークンが必要なエンドポイントの設定
     *
     * @var array
     */
    protected $needsToken = [
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
     * 旧カード会員PINコード認証
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function pin(array $params)
    {
        return $this->request(self::ENDPOINT_PIN, [], $params);
    }

    /**
     * 旧会員メールアドレス認証メール送信
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function checkMail(array $params)
    {
        return $this->request(self::ENDPOINT_CHECK_MAIL, [], $params);
    }

    /**
     * 旧会員カスタマーサービス連絡
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function contact(array $params)
    {
        return $this->request(self::ENDPOINT_CONTACT, [], $params);
    }

    /**
     * 旧会員メールアドレス忘れメールアドレス認証メール送信
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function forgetMail(array $params)
    {
        return $this->request(self::ENDPOINT_FORGET_MAIL_SEND, [], $params);
    }
}
