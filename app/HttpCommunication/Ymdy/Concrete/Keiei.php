<?php

namespace App\HttpCommunication\Ymdy\Concrete;

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
        return $this->request(self::ENDPONT_FETCH_COLORS);
    }

    /**
     * 大事業部マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchDivisionGroups()
    {
        return $this->request(self::ENDPONT_FETCH_DIVISION_GROUPS);
    }

    /**
     * 色マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchDivisions()
    {
        return $this->request(self::ENDPONT_FETCH_DIVISIONS);
    }

    /**
     * 部門グループマスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSectionGroups()
    {
        return $this->request(self::ENDPONT_FETCH_SECTION_GROUPS);
    }

    /**
     * 部門マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSections()
    {
        return $this->request(self::ENDPONT_FETCH_SECTIONS);
    }

    /**
     * 店舗マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchStores()
    {
        return $this->request(self::ENDPONT_FETCH_STORES);
    }

    /**
     * 季節グループマスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSeasonGroups()
    {
        return $this->request(self::ENDPONT_FETCH_SEASON_GROUPS);
    }

    /**
     * 季節マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSeasons()
    {
        return $this->request(self::ENDPONT_FETCH_SEASONS);
    }

    /**
     * サイズマスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSizes()
    {
        return $this->request(self::ENDPONT_FETCH_SIZES);
    }

    /**
     * 取引先マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchCounterParties()
    {
        return $this->request(self::ENDPONT_FETCH_COUNTERPARTIES);
    }
}
