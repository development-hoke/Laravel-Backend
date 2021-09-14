<?php

namespace App\Domain\Utils;

class MemberAuthentication
{
    /**
     * @return int
     */
    public static function getTokenExpiration()
    {
        return config('http_communication.ymdy_member.token_expiration');
    }

    /**
     * @param string $updatedDatetime
     *
     * @return int
     */
    public static function computeExpirationTimestamp(string $updatedDatetime = null)
    {
        return strtotime(sprintf(
            '%s + %d sec',
            $updatedDatetime ?? date('Y-m-d H:i:s'),
            static::getTokenExpiration()
        ));
    }

    /**
     * @param \App\Models\Staff $staff
     *
     * @return int
     */
    public static function computeRemainingTime(\App\Models\Staff $staff)
    {
        return strtotime($staff->token_limit) - time();
    }

    /**
     * @param \App\Models\Staff $staff
     *
     * @return bool
     */
    public static function hasRemainingTime(\App\Models\Staff $staff)
    {
        return static::computeRemainingTime($staff) > 0;
    }
}
