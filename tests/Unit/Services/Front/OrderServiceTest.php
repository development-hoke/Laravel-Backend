<?php

namespace Tests\Unit\Services\Front;

use App\Models\Order;
use App\Services\Front\OrderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        factory(Order::class)->create([
            'id' => 1,
            'code' => '20201119-09967-999-00001',
            'created_at' => '2020-11-19 10:00:00',
        ]);
        factory(Order::class)->create([
            'id' => 2,
            'code' => '20201119-09967-999-00002',
            'created_at' => '2020-11-19 10:00:01',
        ]);
    }

    public function tearDown(): void
    {
        Schema::disableForeignKeyConstraints();
        Order::truncate();
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }

    public function dataCreateCode()
    {
        return [
            '今日初めての受注' => [
                'today' => '2020-11-20',
                'expected' => '20201120-09967-999-00001',
            ],
            '3回目の受注' => [
                'today' => '2020-11-19',
                'expected' => '20201119-09967-999-00003',
            ],
        ];
    }

    /**
     * 受注コード発行
     *
     * @param $today
     * @param $expected
     * @dataProvider dataCreateCode
     */
    public function testCreateCode($today, $expected)
    {
        Carbon::setTestNow($today);
        $purchaseService = resolve(OrderService::class);
        $this->assertEquals($expected, $purchaseService->createCode());
    }
}
