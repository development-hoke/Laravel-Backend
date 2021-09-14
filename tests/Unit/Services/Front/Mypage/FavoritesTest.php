<?php

namespace Tests\Unit\Services\Front\Mypage;

use App\Criteria\Item\FrontMypageFavoriteCriteria;
use App\Models\Item;
use App\Services\Front\ItemService;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreateTestData;
use Tests\Concerns\RefreshDatabaseLite;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabaseLite;
    use CreateTestData;

    private $items = [];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->createApplication();
    }

    public function dataFavorites()
    {
        $this->truncateDatabase();
        $this->beforeCreateItem();

        $this->items = factory(Item::class, 41)->state('published')->create();
        factory(Item::class)->state('unpublished')->create();

        return [
            'お気に入り商品なし' => [
                'params' => [
                    FrontMypageFavoriteCriteria::KEY => true,
                    'member_id' => '200000035',
                ],
                'expected' => [0],
                'creates' => [],
            ],
            'お気に入り商品1つ' => [
                'params' => [
                    FrontMypageFavoriteCriteria::KEY => true,
                    'member_id' => '200000035',
                ],
                'expected' => [1],
                'creates' => [
                    [
                        'item_id' => $this->items[0]->id,
                        'member_id' => '200000035',
                    ],
                ],
            ],
            'お気に入り商品2つ' => [
                'params' => [
                    FrontMypageFavoriteCriteria::KEY => true,
                    'member_id' => '200000035',
                ],
                'expected' => [2],
                'creates' => [
                    [
                        'item_id' => $this->items[0]->id,
                        'member_id' => '200000035',
                    ],
                    [
                        'item_id' => $this->items[1]->id,
                        'member_id' => '200000035',
                    ],
                ],
            ],
            'お気に入り商品2つ(別ユーザー有り)' => [
                'params' => [
                    FrontMypageFavoriteCriteria::KEY => true,
                    'member_id' => '200000035',
                ],
                'expected' => [2],
                'creates' => [
                    [
                        'item_id' => $this->items[0]->id,
                        'member_id' => '200000035',
                    ],
                    [
                        'item_id' => $this->items[1]->id,
                        'member_id' => '200000035',
                    ],
                    [
                        'item_id' => $this->items[2]->id,
                        'member_id' => '200000036',
                    ],
                ],
            ],
            'お気に入り商品ページング' => [
                'params' => [
                    FrontMypageFavoriteCriteria::KEY => true,
                    'member_id' => '200000035',
                ],
                'expected' => [40, 1],
                'creates' => $this->items->map(function ($item) {
                    return [
                        'item_id' => $item->id,
                        'member_id' => '200000035',
                    ];
                }),
            ],
        ];
    }

    protected function truncateDatabase()
    {
        $this->truncateBeforeCreateItem();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Item::truncate();
        \App\Models\ItemDetail::truncate();
        \App\Models\ItemDetailIdentification::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * カート統合処理テスト
     *
     * @param $params
     * @param $expected
     * @dataProvider dataFavorites
     */
    public function testFavorites($params, $expected, $creates)
    {
        foreach (@$creates as $create) {
            \App\Models\ItemFavorite::create([
                'member_id' => $create['member_id'],
                'item_id' => $create['item_id'],
            ]);
        }

        // 処理実行
        $itemService = resolve(ItemService::class);
        $items = $itemService->search($params);
        $this->assertCount($expected[0], $items);

        // todo: 2ページ目のテスト
        // if (isset($expected[1])) {
        //     $params['page'] = 2;
        //     $items = $itemService->search($params);
        //     $this->assertCount($expected[1], $items);
        // }
    }
}
