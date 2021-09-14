<?php

namespace App\Http\Controllers\Api\V1\Front\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * @param Request $request
     *
     * @return []
     */
    public function getNewItems(Request $request)
    {
        return [
            'next_page' => '/api/v1/contents/new_items?page=2',
            'new_items' => [
                [
                    'sort' => 3,
                    'image' => 'https://g02.future-shop.jp/shop/item/ymdy/design/img01/nouer_face.jpg',
                    'name' => 'リネンライクフレアスカート',
                    'number' => '3',
                    'retail_price' => 0,
                    'price' => 0,
                    'stock' => 0,
                    'status' => 1,
                    'favorite_id' => 13,
                ], [
                    'sort' => 4,
                    'image' => 'https://g02.future-shop.jp/shop/item/ymdy/design/img01/scotclub_face.jpg',
                    'name' => 'ラップ風異素材ドッキングワンピース',
                    'number' => '4',
                    'retail_price' => 0,
                    'price' => 1000,
                    'stock' => 20,
                    'status' => 1,
                    'favorite_id' => 25,
                ],
            ],
        ];
    }

    /**
     * @param Request $request
     *
     * @return []
     */
    public function getPickups(Request $request)
    {
        return [
            'page' => 10,
            'next_page' => '/api/v1/contents/pickups?page=2',
            'pickups' => [
                [
                    'sort' => 3,
                    'image' => 'https://g02.future-shop.jp/shop/item/ymdy/design/img01/nouer_face.jpg',
                    'name' => 'リネンライクフレアスカート',
                    'number' => '10',
                    'retail_price' => 0,
                    'price' => 0,
                    'stock' => 0,
                    'status' => 1,
                    'favorite_id' => 13,
                ], [
                    'sort' => 4,
                    'image' => 'https://g02.future-shop.jp/shop/item/ymdy/design/img01/scotclub_face.jpg',
                    'name' => 'ラップ風異素材ドッキングワンピース',
                    'number' => '11',
                    'retail_price' => 0,
                    'price' => 1000,
                    'stock' => 20,
                    'status' => 1,
                    'favorite_id' => 25,
                ],
            ],
        ];
    }
}
