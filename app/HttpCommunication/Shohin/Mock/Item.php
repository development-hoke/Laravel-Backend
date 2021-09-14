<?php

namespace App\HttpCommunication\Shohin\Mock;

use App\HttpCommunication\Response\Mock\Response;
use App\HttpCommunication\Shohin\HttpCommunicationService;
use App\HttpCommunication\Shohin\ItemInterface;

class Item extends HttpCommunicationService implements ItemInterface
{
    /**
     * configを取得するためのキー
     *
     * @return string
     */
    protected function getConfigKey(): string
    {
        return 'shohin';
    }

    /**
     * 商品情報取得
     *
     * @param array $params
     *
     * @return Response
     */
    public function fetchMasters(array $params)
    {
        // todo 実データと値の確認
        return new Response([
            'status' => 1,
            'header' => [
                'offset' => @$params['offset'],
                'limit' => @$params['limit'],
                'count' => 1,
            ],
            'data' => [
                [
                    'Item' => [
                        'id' => '1',
                        'code' => '031310002905',
                        'created' => '2021-01-20 20:06:53',
                        'modified' => '2021-01-20 20:06:53',
                        'item_master_id' => '6',
                        'mst_item_prop_id' => '6',
                        'mst_item_status_id' => '6',
                        'storage_id' => '1',
                    ],
                    'ItemMaster' => [
                        'id' => '6',
                        'created' => '2021-01-20 18:37:11',
                        'modified' => '2021-01-20 18:37:11',
                        'name' => '商品B-森中',
                        'code' => '031310002905',
                        'code225' => '',
                        'period' => '1',
                        'maker_cd' => '0088',
                        'status' => '1',
                        'retail_price' => 2000,
                        'retail_tax' => '1000',
                        'wholesale_price' => 5500,
                        'wholesale_tax' => '500',
                        'tax_rate' => '0',
                        'buyer_user_id' => '1',
                        'material_cd' => '1',
                        'material' => 'あ',
                        'pattern_no' => '1',
                        'mixture_ratio' => '1',
                        'notice' => '2',
                        'estimated_cost' => '3',
                        'sale_month' => '4',
                        'delivery_date' => '2021-01-19 00:00:00',
                        'mst_delivery_method_id' => '1',
                        'modified_user_id' => '1',
                        'order_date' => '2021-01-21 00:00:00',
                        'mst_item_group_id' => '1',
                        'mst_fashion_velocity_id' => '1',
                        'item_online_tags' => '',
                        'item_online_categories' => '',
                        'item_images' => '',
                        'description' => '',
                    ],
                ],
            ],
        ]);
    }

    /**
     * 在庫情報取得
     *
     * @param array $params
     *
     * @return Response
     */
    public function fetchStocks(array $params)
    {
        // todo 実データと値の確認
        return new Response([
            'status' => 1,
            'header' => [
                'offset' => @$params['offset'],
                'limit' => @$params['limit'],
                'count' => 1,
            ],
            'data' => [
                [
                    'Item' => [
                        'id' => '1',
                        'code2241' => '9988777765543',
                        'created' => '2021-01-20 20:06:53',
                        'modified' => '2021-01-20 20:06:53',
                        'item_master_id' => '6',
                        'mst_item_prop_id' => '6',
                        'item_status_id' => '1',
                        'storage_id' => '1',
                    ],
                    'ItemMaster' => [
                        'id' => '6',
                        'created' => '2021-01-20 18:37:11',
                        'modified' => '2021-01-20 18:37:11',
                        'name' => '商品B-森中',
                        'code' => '9988777765543',
                        'code225' => '',
                        'period' => '1',
                        'maker_cd' => '0088',
                        'status' => '1',
                        'retail_price' => 2000,
                        'retail_tax' => '1000',
                        'wholesale_price' => 5500,
                        'wholesale_tax' => '500',
                        'tax_rate' => '0',
                        'buyer_user_id' => '1',
                        'material_cd' => '1',
                        'material' => 'あ',
                        'pattern_no' => '1',
                        'mixture_ratio' => '1',
                        'notice' => '2',
                        'estimated_cost' => '3',
                        'sale_month' => '4',
                        'delivery_date' => '2021-01-19 00:00:00',
                        'mst_delivery_method_id' => '1',
                        'modified_user_id' => '1',
                        'order_date' => '2021-01-21 00:00:00',
                        'mst_item_group_id' => '1',
                        'mst_fashion_velocity_id' => '1',
                        'item_online_tags' => '',
                        'item_online_categories' => '',
                        'item_images' => '',
                        'description' => '',
                    ],
                ],
            ],
        ]);
    }

    /**
     * 購買時ポイント計算
     *
     * @param array $body
     *
     * @return Response
     */
    public function purchase(array $body)
    {
        // todo 実データと値の確認
        return new Response([
            'order_items' => [],
            'order_id' => 1,
            'order_date' => '2020-11-30',
            'payment_type' => 1,
            'price' => 1000,
            'discount_rate' => 10,
            'discount_memo' => '',
            'tax' => 10,
            'fee' => 10,
            'use_point' => 10,
            'order_type' => 1,
            'memo1' => 'testmemo',
            'memo2' => 'testmemo',
            'billing_pref_id' => 'testmemo',
            'billing_city' => 'testmemo',
            'billing_town' => 'testmemo',
            'billing_address' => 'testmemo',
            'billing_building' => 'testmemo',
            'billing_tel' => 'testmemo',
            'delivery_pref_id' => 'testmemo',
            'delivery_city' => 'testmemo',
            'delivery_town' => 'testmemo',
            'delivery_address' => 'testmemo',
            'delivery_building' => 'testmemo',
            'delivery_tel' => 'testmemo',
            'delivery_hope_date' => 1000,
            'delivery_hope_time' => 500,
        ]);
    }

    /**
     * 注文キャンセル
     *
     * @param string $code
     *
     * @return ResponseInterface
     */
    public function purchaseCancel(string $code)
    {
        return new Response();
    }

    /**
     * EC情報変更
     *
     * @param array $body
     *
     * @return ResponseInterface
     */
    public function ecUpdate(array $body)
    {
        return new Response();
    }
}
