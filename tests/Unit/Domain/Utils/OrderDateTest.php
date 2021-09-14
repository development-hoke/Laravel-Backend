<?php

namespace Tests\Unit\Domain\Utils;

use App\Domain\Utils\OrderCancel;
use Carbon\Carbon;
use Tests\TestCase;

class OrderDateTest extends TestCase
{
    public function dataCanCancelDatetime()
    {
        return [
            '受注してから1時間以内' => [
                'now' => '2021-01-10 11:00:00',
                'expected' => true,
            ],
            '受注してから1時間1秒経過' => [
                'now' => '2021-01-10 11:00:01',
                'expected' => false,
            ],
        ];
    }

    /**
     * @param $now
     * @param $expected
     * @dataProvider dataCanCancelDatetime
     */
    public function testCanCancelDatetime($now, $expected)
    {
        // 現在時刻をテスト用に設定
        Carbon::setTestNow($now);
        $orderData = new Carbon('2021-01-10 10:00:00');
        $result = OrderCancel::canCancelDatetime($orderData);
        $this->assertEquals($expected, $result);
    }

    public function dataCanCancelStatus()
    {
        return [
            '受注' => [
                'status' => 1,
                'expected' => true,
            ],
            '入荷済み' => [
                'status' => 2,
                'expected' => true,
            ],
            '発送済' => [
                'status' => 3,
                'expected' => true,
            ],
            '保留' => [
                'status' => 4,
                'expected' => true,
            ],
            '納品(完了)' => [
                'status' => 5,
                'expected' => true,
            ],
            'キャンセル(完了)' => [
                'status' => 6,
                'expected' => false,
            ],
            '返品(完了)' => [
                'status' => 6,
                'expected' => false,
            ],
            '変更されたので過去ログ行き' => [
                'status' => 1,
                'expected' => true,
            ],
        ];
    }

    /**
     * キャンセル可能か判定テスト
     *
     * @param $status
     * @param $expected
     * @dataProvider dataCanCancelStatus
     */
    public function testCanCancelStatus($status, $expected)
    {
        $result = OrderCancel::canCancelStatus($status);
        $this->assertEquals($expected, $result);
    }
}
