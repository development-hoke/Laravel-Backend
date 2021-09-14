<?php

namespace Tests\Unit\Domain\Utils;

use App\Domain\Utils\OrderCode;
use App\Models\Order;
use Carbon\Carbon;
use Tests\TestCase;

class OrderCodeTest extends TestCase
{
    /**
     * 新しいコード発番テスト
     */
    public function testCreateNewCode()
    {
        $preCode = '20201120-09967-999-00001';
        $result = $this->doPrivateMethod(new OrderCode(), 'createNewCode', $preCode);
        $this->assertEquals('00002', $result);
    }

    public function dataGetCode()
    {
        return [
            '本日最初の注文の場合' => [
                'today' => '20201120',
                'order' => null,
                'expected' => '20201120-09967-999-00001',
            ],
            '2回目の注文の場合' => [
                'today' => '20201120',
                'order' => new Order([
                    'code' => '20201120-09967-999-00001',
                ]),
                'expected' => '20201120-09967-999-00002',
            ],
        ];
    }

    /**
     * @param $today
     * @param $lastOrder
     * @param $expected
     * @dataProvider dataGetCode
     */
    public function testGetCode($today, $lastOrder, $expected)
    {
        Carbon::setTestNow($today);
        $result = OrderCode::getCode($lastOrder);
        $this->assertEquals($expected, $result);
    }
}
