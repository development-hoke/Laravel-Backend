<?php

namespace App\Domain;

interface MemberAuthInterface
{
    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberToken(string $token);

    /**
     * スタッフトークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token);

    /**
     * @param array $data
     * @param string $email
     *
     * @return \App\Models\User
     */
    public function saveMemberTokenAsUser(array $data, string $email);

    /**
     * @param array $data
     * @param string $email
     *
     * @return \App\Models\User
     */
    public function saveAgentLoggingIn(array $data, string $email);

    /**
     * トークンをリフレッシュして新しいトークンをusersテーブルに保存する
     *
     * @return \App\Models\User
     */
    public function tokenRefresh();
}
