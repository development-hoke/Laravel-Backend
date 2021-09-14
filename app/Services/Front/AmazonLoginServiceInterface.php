<?php

namespace App\Services\Front;

interface AmazonLoginServiceInterface
{
    /**
     * Amazonアカウントと会員IDを紐付ける
     *
     * @param string $accessToken
     *
     * @return \App\Models\User
     */
    public function linkAccount(string $accessToken);

    /**
     * Amazonのトークンを利用して、会員ポイントシステムのトークンをリフレッシュする。
     *
     * @param string $accessToken
     *
     * @return \App\Models\User
     */
    public function auth(string $accessToken);

    /**
     * アクセストークンを使用してuserを取得
     *
     * @param string $accessToken
     *
     * @return \App\Models\User
     */
    public function findUserByAccessToken(string $accessToken);
}
