<?php

namespace Tests\Unit\Domain\Utils;

use App\Domain\Utils\Cart;
use Carbon\Carbon;
use Tests\TestCase;

class CartTest extends TestCase
{
    public function dataCalculateValidTime()
    {
        return [
            '残り1分' => [
                'now' => '2021-02-11 11:29:00',
                'posted_at' => '2021-02-11 10:00:00',
                'expected' => 1,
            ],
            '残り90分' => [
                'now' => '2021-02-11 10:00:00',
                'posted_at' => '2021-02-11 10:00:00',
                'expected' => 90,
            ],
            '残り1分未満' => [
                'now' => '2021-02-11 11:29:30',
                'posted_at' => '2021-02-11 10:00:00',
                'expected' => 1,
            ],
            '1日前' => [
                'now' => '2021-02-11 10:00:00',
                'posted_at' => '2021-02-10 10:00:00',
                'expected' => 0,
            ],
        ];
    }

    /**
     * @param $now
     * @param $postedAt
     * @param $expected
     * @dataProvider dataCalculateValidTime
     */
    public function testCalculateValidTime($now, $postedAt, $expected)
    {
        Carbon::setTestNow($now);
        $validTime = Cart::calculateValidTime($postedAt);
        $this->assertEquals($expected, $validTime);
    }
}
