<?php

namespace App\Domain\Utils;

use App\Enums\AdminLog\Type as EnumType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class AdminLog
{
    /**
     * ルートプレフィックス
     */
    const ROUTE_PATH_PREFIX = 'api/v1/admin';

    /**
     * リファラ参照用のヘッダ
     */
    const REFERER_HEADER = 'Admin-Referer';

    /**
     * @var array
     */
    private static $titles;

    /**
     * @var array
     */
    private static $method2crud = [
        Request::METHOD_GET => EnumType::Read,
        Request::METHOD_POST => EnumType::Create,
        Request::METHOD_PUT => EnumType::Update,
        Request::METHOD_DELETE => EnumType::Delete,
    ];

    /**
     * @var array
     */
    private static $crud2method = [];

    /**
     * @return void
     */
    public static function initDict()
    {
        foreach (static::$method2crud as $method => $crud) {
            static::$crud2method[$crud] = $method;
        }
    }

    /**
     * CRUDの定数からHTTPメソッドを取得する
     *
     * @param int $crud
     *
     * @return string
     */
    public static function c2m(int $crud)
    {
        if (empty(static::$crud2method)) {
            static::initDict();
        }

        return static::$crud2method[$crud] ?? null;
    }

    /**
     * HTTPメソッドからCRUDの定数を取得する
     *
     * @param string $method
     *
     * @return string
     */
    public static function m2c(string $method)
    {
        return static::$method2crud[$method] ?? null;
    }

    /**
     * @return array
     */
    public static function getTitles()
    {
        if (!isset(static::$titles)) {
            static::$titles = Lang::get('action.admin');
        }

        return static::$titles;
    }

    /**
     * @param string $method
     * @param string $path
     *
     * @return string
     */
    public static function resolveRouteNameToTitle(string $routeName)
    {
        $titles = static::getTitles();

        return $titles[$routeName] ?? null;
    }
}
