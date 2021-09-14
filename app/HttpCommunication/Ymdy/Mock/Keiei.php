<?php

namespace App\HttpCommunication\Ymdy\Mock;

use App\HttpCommunication\Response\Mock\Response;
use App\HttpCommunication\Ymdy\HttpCommunicationService;
use App\HttpCommunication\Ymdy\KeieiInterface;

/*
 * 基幹認証
 * 管理画面のログイン情報や、どのスタッフがどの画面を操作出来るかなどの情報を取得するシステムです。
 */
class Keiei extends HttpCommunicationService implements KeieiInterface
{
    /**
     * configを取得するためのキー
     *
     * @return string
     */
    protected function getConfigKey(): string
    {
        return 'ymdy_keiei';
    }

    /**
     * 色マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchColors()
    {
        return new Response(require __DIR__.'/fixtures/color_list.php');
    }

    /**
     * 大事業部マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchDivisionGroups()
    {
        return new Response(require __DIR__.'/fixtures/division_group_list.php');
    }

    /**
     * 色マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchDivisions()
    {
        return new Response(require __DIR__.'/fixtures/division_list.php');
    }

    /**
     * 部門グループマスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSectionGroups()
    {
        return new Response(require __DIR__.'/fixtures/section_group_list.php');
    }

    /**
     * 部門マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSections()
    {
        return new Response(require __DIR__.'/fixtures/section_list.php');
    }

    /**
     * 店舗マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchStores()
    {
        return new Response(require __DIR__.'/fixtures/shop_list.php');
    }

    /**
     * 季節グループマスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSeasonGroups()
    {
        return new Response(require __DIR__.'/fixtures/season_group_list.php');
    }

    /**
     * 季節マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSeasons()
    {
        return new Response(require __DIR__.'/fixtures/season_list.php');
    }

    /**
     * サイズマスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSizes()
    {
        return new Response(require __DIR__.'/fixtures/size_list.php');
    }

    /**
     * 取引先マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchCounterParties()
    {
        return new Response(require __DIR__.'/fixtures/counter_parties_list.php');
    }
}
