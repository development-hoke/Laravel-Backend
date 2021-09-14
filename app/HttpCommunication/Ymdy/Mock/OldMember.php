<?php

namespace App\HttpCommunication\Ymdy\Mock;

use App\HttpCommunication\Response\Mock\Response;
use App\HttpCommunication\Ymdy\HttpCommunicationService;
use App\HttpCommunication\Ymdy\OldMemberInterface;

/**
 * 会員・ポイントシステムとの連携で使用する（モック）
 * 会員ポイントシステム
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OldMember extends HttpCommunicationService implements OldMemberInterface
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
     * 旧カード会員PINコード認証
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function pin(array $params)
    {
        return new Response(require __DIR__.'/fixtures/pin.php');
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
        return new Response('success');
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
        return new Response('success');
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
        return new Response('success');
    }
}
