<?php

namespace App\HttpCommunication\Ymdy;

use App\HttpCommunication\HttpCommunicationServiceInterface;

interface KeieiInterface extends HttpCommunicationServiceInterface
{
    const ENDPONT_FETCH_COLORS = 'fetch_colors';
    const ENDPONT_FETCH_DIVISION_GROUPS = 'fetch_division_groups';
    const ENDPONT_FETCH_DIVISIONS = 'fetch_divisions';
    const ENDPONT_FETCH_SECTION_GROUPS = 'fetch_section_groups';
    const ENDPONT_FETCH_SECTIONS = 'fetch_sections';
    const ENDPONT_FETCH_STORES = 'fetch_stores';
    const ENDPONT_FETCH_SEASON_GROUPS = 'fetch_season_groups';
    const ENDPONT_FETCH_SEASONS = 'fetch_seasons';
    const ENDPONT_FETCH_SIZES = 'fetch_sizes';
    const ENDPONT_FETCH_COUNTERPARTIES = 'fetch_counter_parties';

    /**
     * 色マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchColors();

    /**
     * 大事業部マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchDivisionGroups();

    /**
     * 事業部マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchDivisions();

    /**
     * 部門グループマスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSectionGroups();

    /**
     * 部門マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSections();

    /**
     * 店舗マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchStores();

    /**
     * 季節グループマスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSeasonGroups();

    /**
     * 季節マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSeasons();

    /**
     * サイズマスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchSizes();

    /**
     * 取引先マスタ一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchCounterParties();
}
