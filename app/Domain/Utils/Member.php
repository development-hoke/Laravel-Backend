<?php

namespace App\Domain\Utils;

class Member
{
    /**
     * スタッフアカウントの判定
     *
     * @param array $member
     *
     * @return bool
     */
    public static function isStaffAccount(array $member)
    {
        return !empty($member['staff_code']);
    }
}
