<?php

namespace App\Domain\Contracts;

interface AssignableCrediencalToken
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
}
