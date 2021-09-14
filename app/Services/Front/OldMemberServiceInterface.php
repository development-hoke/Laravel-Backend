<?php

namespace App\Services\Front;

interface OldMemberServiceInterface
{
    /**
     * 旧カード会員PINコード認証
     *
     * @param array $params
     *
     * @return array
     */
    public function pin(array $params);

    /**
     * 旧会員メールアドレス認証メール送信
     *
     * @param array $params
     *
     * @return array
     */
    public function checkMail(array $params);

    /**
     * 旧会員カスタマーサービス連絡
     *
     * @param array $params
     *
     * @return array
     */
    public function forgetAll(array $params);

    /**
     * 新会員移行手続きメールアドレス忘れ
     *
     * @param array $params
     *
     * @return array
     */
    public function forgetMail(array $params);
}
