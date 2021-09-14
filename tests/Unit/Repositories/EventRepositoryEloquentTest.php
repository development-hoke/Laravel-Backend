<?php

namespace Tests\Unit\Repositories;

use App\Models\Event;
use App\Repositories\EventRepositoryEloquent;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EventRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        factory(\App\Models\Event::class)->create([
            'period_from' => Carbon::now()->subYear()->format('Y-m-d H:i:s'),
            'period_to' => Carbon::now()->addYear()->format('Y-m-d H:i:s'),
            'published' => \App\Enums\Common\Boolean::IsTrue,
            'delivery_condition' => 4000,
            'delivery_price' => 0,
        ]);
    }

    public function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Event::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    public function dataGetDeliveryPrice()
    {
        return [
            '送料無料 条件と同額' => [
                4000,
                0,
            ],
            '送料無料 条件より高額' => [
                5000,
                0,
            ],
            '割引適用外 条件より低額' => [
                3900,
                510,
            ],
        ];
    }

    // /**
    //  * @param $itemsTotal
    //  * @param $expected
    //  * @dataProvider dataGetDeliveryPrice
    //  */
    // public function testGetDeliveryPrice($itemsTotal, $expected)
    // {
    //     $eventRepository = app()->make(EventRepositoryEloquent::class);
    //     $result = $eventRepository->getDeliveryPrice($itemsTotal);
    //     $this->assertEquals($expected, $result);
    // }

    // public function dataGetByPeriodFrom()
    // {
    //     return [
    //         '商品に紐づくイベントが存在しない場合 nullを取得' => [
    //             'events' => collect([]),
    //             'expected' => null,
    //         ],
    //         '商品に紐づくイベントが1つの場合 eventを取得' => [
    //             'events' => collect([
    //                 new Event([
    //                     'period_from' => '2021-01-10 10:00:00',
    //                 ]),
    //             ]),
    //             'expected' => [
    //                 'period_from' => '2021-01-10 10:00:00',
    //                 'delivery_condition' => 0,
    //             ],
    //         ],
    //         '商品に紐づくイベントが2つの場合 開始日の最新のeventを取得' => [
    //             'events' => collect([
    //                 new Event([
    //                     'period_from' => '2021-01-10 10:00:00',
    //                 ]),
    //                 new Event([
    //                     'period_from' => '2021-01-11 10:00:00',
    //                 ]),
    //             ]),
    //             'expected' => [
    //                 'period_from' => '2021-01-11 10:00:00',
    //                 'delivery_condition' => 0,
    //             ],
    //         ],
    //     ];
    // }

    /**
     * 商品に紐づくイベントの取得
     *
     * @param $events
     * @param $expected
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @dataProvider dataGetByPeriodFrom
     */
    public function testGetByPeriodFrom($events, $expected)
    {
        $eventRepository = app()->make(EventRepositoryEloquent::class);
        $result = $eventRepository->getByPeriodFrom($events);
        if ($expected) {
            $this->assertEquals($expected, $result->toArray());
        } else {
            $this->assertEquals($expected, $result);
        }
    }
}
