<?php

namespace App\Entities\AmazonPay;

use App\Entities\Entity;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/address.html
 *
 * @property string $name 名前または会社名
 * @property string|null $address_line1 住所ライン1 1〜3のどれか一つは必須
 * @property string|null $address_line2 住所ライン2
 * @property string|null $address_line3 住所ライン3
 * @property string $state_or_region 都道府県 (Amazon Payの値)
 * @property string $pref_name prefs.name
 * @property string $pref_id prefs.id
 * @property string $postal_code 郵便番号
 * @property string $country_code 国コード（ISO3166形式）
 */
class Address extends Entity
{
    /**
     * @param array $data
     *
     * @return array
     */
    protected function toAttributes($data)
    {
        $data = parent::toAttributes($data);

        $prefName = \App\Utils\Pref::normalizeName($data['state_or_region']);
        $prefId = \App\Utils\Pref::n2i($prefName);
        $data['pref_name'] = $prefName;
        $data['pref_id'] = $prefId;

        return $data;
    }
}
