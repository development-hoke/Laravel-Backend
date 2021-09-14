<?php

namespace App\Http\Controllers\Api\V1\Front\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Styling\IndexRequest;
use App\Http\Resources\Styling as StylingResource;
use App\Services\Front\StylingServiceInterface as StylingService;
use Illuminate\Http\Request;

class StylingController extends Controller
{
    /**
     * @var StylingService
     */
    private $stylingService;

    /**
     * @param StylingService $stylingService
     */
    public function __construct(StylingService $stylingService)
    {
        $this->stylingService = $stylingService;
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $stylings = $this->stylingService->search($request->validated());

        $stylings->setPath($request->url());
        $stylings->appends($request->query());

        return StylingResource::collection($stylings);
    }

    /**
     * @param Request $request
     *
     * @return []
     */
    public function show(Request $request)
    {
        return [
            'id' => 13,
            'main_image' => 'https://g02.future-shop.jp/shop/item/ymdy/design/img01/nouer_face.jpg',
            'sub_images' => [
                [
                    'subimage' => 'https://g02.future-shop.jp/shop/item/ymdy/design/img01/nouer_face.jpg',
                ],
            ],
            'tall' => 0,
            'staff_name' => 'SCOTCLUB',
            'staff_tall' => 0,
            'staff_image' => 'https://g02.future-shop.jp/shop/item/ymdy/design/img01/nouer_face.jpg',
            'brand_name' => 'ブランド',
            'comment' => '春コードまとめ',
            'store_name' => 'SCOTCLUB',
            'tags' => [
                [
                    'id' => 13,
                    'name' => 'ブーツ',
                ], [
                    'id' => 13,
                    'name' => 'Tシャツ',
                ],
            ],
            'created_at' => '2020-10-01 00:00:00',
            'published_at' => '2020-10-01 09:00:00',
            'pv_count' => 0,
            'items' => [
                [
                    'id' => 10,
                    'term_id' => 0,
                    'season_id' => 0,
                    'organization_id' => 0,
                    'division_id' => 0,
                    'department_id' => 0,
                    'product_number' => 'BOOTS0000001',
                    'maker_product_number' => '1000000001',
                    'fashion_speed' => 0,
                    'name' => 'Vanishブーツ',
                    'retail_price' => 0,
                    'price_change_period' => '2020-07-13 10:00:00',
                    'price_change_rate' => 0,
                    'main_store_brand' => 0,
                    'brand_id' => 0,
                    'display_name' => 'Vanishブーツ',
                    'discount_rate' => 0,
                    'is_member_discount' => true,
                    'member_discount_rate' => 0,
                    'point_rate' => 0,
                    'sales_period_from' => '2020-07-13 10:00:00',
                    'sales_period_to' => '2020-07-13 10:00:00',
                    'description' => 'テキストが入ります。テキストが入ります。テキストが入ります。',
                    'note_staff_ok' => 'string',
                    'size_caution' => 'string',
                    'material_caution' => 'string',
                    'status' => 0,
                    'reserve_status' => 0,
                    'returnable' => true,
                    'item_sale_types' => [
                        [
                            'id' => 10,
                            'item_id' => 10,
                            'sales_type_id' => 10,
                            'sort' => 0,
                        ],
                    ],
                    'online_categoryies' => [
                        [
                            'id' => 10,
                            'item_id' => 10,
                            'sales_type_id' => 10,
                        ],
                    ],
                    'online_tags' => [
                        [
                            'id' => 1,
                            'item_id' => 20,
                            'sales_type_id' => 5,
                        ],
                    ],
                ],
            ],
            'cordinate_id' => 13,
        ];
    }
}
