<?php

namespace App\Utils;

use Jenssegers\Agent\Agent;

class UserAgent
{
    /**
     * @var Agent
     */
    private static $agent;

    /**
     * @param Agent $agent
     *
     * @return void
     */
    public static function initAgentIfNull($agent = null)
    {
        if (!isset(static::$agent)) {
            static::$agent = $agent ?? new Agent();
        }
    }

    /**
     * @return \Jenssegers\Agent\Agent
     */
    public static function getAgent()
    {
        static::initAgentIfNull();

        return static::$agent;
    }

    /**
     * @return bool
     */
    public static function isMobile()
    {
        return static::getAgent()->isMobile();
    }

    /**
     * @return bool
     */
    public static function isTablet()
    {
        return static::getAgent()->isTablet();
    }

    /**
     * @return bool
     */
    public static function isPc()
    {
        return (static::isMobile() || static::isTablet()) === false;
    }

    /**
     * @return int \App\Enums\Common\DeviceType
     */
    public static function getDeviceType()
    {
        switch (true) {
            case static::isMobile():
                return \App\Enums\Common\DeviceType::Mobile;

            case static::isTablet():
                return \App\Enums\Common\DeviceType::Tablet;

            case static::isPc():
            default:
                return \App\Enums\Common\DeviceType::Pc;
        }
    }
}
