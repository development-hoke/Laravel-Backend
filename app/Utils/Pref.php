<?php

namespace App\Utils;

class Pref
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private static $prefs;

    /**
     * IDを添字に持つPrefの配列
     *
     * @var array
     */
    private static $idDict;

    /**
     * nameを添字に持つPrefの配列
     *
     * @var array
     */
    private static $nameDict;

    /**
     * nameを添字に持つPrefの配列 (都道府県なし)
     *
     * @var array
     */
    private static $shortNameDict;

    /**
     * 英語表記
     *
     * @var array
     */
    private static $enNameDict = [
        'Hokkaido' => '北海道',
        'Aomori' => '青森県',
        'Iwate' => '岩手県',
        'Miyagi' => '宮城県',
        'Akita' => '秋田県',
        'Yamagata' => '山形県',
        'Fukushima' => '福島県',
        'Ibaraki' => '茨城県',
        'Tochigi' => '栃木県',
        'Gunma' => '群馬県',
        'Saitama' => '埼玉県',
        'Chiba' => '千葉県',
        'Tokyo' => '東京都',
        'Kanagawa' => '神奈川県',
        'Niigata' => '新潟県',
        'Toyama' => '富山県',
        'Ishikawa' => '石川県',
        'Fukui' => '福井県',
        'Yamanashi' => '山梨県',
        'Nagano' => '長野県',
        'Gifu' => '岐阜県',
        'Shizuoka' => '静岡県',
        'Aichi' => '愛知県',
        'Mie' => '三重県',
        'Shiga' => '滋賀県',
        'Kyoto' => '京都府',
        'Osaka' => '大阪府',
        'Hyogo' => '兵庫県',
        'Nara' => '奈良県',
        'Wakayama' => '和歌山県',
        'Tottori' => '鳥取県',
        'Shimane' => '島根県',
        'Okayama' => '岡山県',
        'Hiroshima' => '広島県',
        'Yamaguchi' => '山口県',
        'Tokushima' => '徳島県',
        'Kagawa' => '香川県',
        'Ehime' => '愛媛県',
        'Kochi' => '高知県',
        'Fukuoka' => '福岡県',
        'Saga' => '佐賀県',
        'Nagasaki' => '長崎県',
        'Kumamoto' => '熊本県',
        'Oita' => '大分県',
        'Miyazaki' => '宮崎県',
        'Kagoshima' => '鹿児島県',
        'Okinawa' => '沖縄県',
    ];

    /**
     * @return void
     */
    public static function load()
    {
        static::$prefs = resolve(\App\Repositories\PrefRepository::class)->all();
        static::$idDict = [];
        static::$nameDict = [];
        static::$shortNameDict = [];

        $prefs = static::$prefs;

        foreach ($prefs as $pref) {
            static::$idDict[$pref->id] = $pref;
            static::$nameDict[$pref->name] = $pref;
            static::$shortNameDict[
                mb_ereg_replace('(都|道|府|県)$', '', $pref->name)
            ] = $pref;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPrefs()
    {
        if (!isset(static::$prefs)) {
            static::load();
        }

        return static::$prefs;
    }

    /**
     * @return array
     */
    public static function getIdDict()
    {
        if (!isset(static::$idDict)) {
            static::load();
        }

        return static::$idDict;
    }

    /**
     * @return array
     */
    public static function getNameDict()
    {
        if (!isset(static::$nameDict)) {
            static::load();
        }

        return static::$nameDict;
    }

    /**
     * @return array
     */
    public static function getShortNameDict()
    {
        if (!isset(static::$shortNameDict)) {
            static::load();
        }

        return static::$shortNameDict;
    }

    /**
     * IDからNameへ変換
     *
     * @param int $id
     *
     * @return string|null
     */
    public static function i2n(int $id)
    {
        $dict = static::getIdDict();

        if (!isset($dict[$id])) {
            return null;
        }

        return $dict[$id]->name;
    }

    /**
     * NameからIDへ変換
     *
     * @param string $name
     *
     * @return int|null
     */
    public static function n2i(string $name)
    {
        $dict = static::getNameDict();

        if (!isset($dict[$name])) {
            return null;
        }

        return $dict[$name]->id;
    }

    /**
     * Nameをフォーマットする
     *
     * @param string $name
     *
     * @return string
     */
    public static function normalizeName(string $name)
    {
        $name = mb_convert_kana(trim($name), 'KVs');

        if (preg_match('/(都|道|府|県)$/', $name)) {
            return $name;
        }

        $dict = static::getShortNameDict();

        if (isset($dict[$name])) {
            return $dict[$name]->name;
        }

        if (isset(static::$enNameDict[$name])) {
            return static::$enNameDict[$name];
        }

        return null;
    }
}
