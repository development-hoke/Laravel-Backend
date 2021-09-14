<?php

namespace Tests\Unit\Utils;

use App\Utils\Tax;
use Carbon\Carbon;
use Tests\TestCase;

class TaxTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2020-11-20');
    }

    /**
     * 元金を含む消費税率取得テスト
     */
    public function testGetRateWithOrigin()
    {
        $result = Tax::getRateWithOrigin();
        $this->assertEquals(1.1, $result);
    }

    /**
     * 消費税率取得テスト
     */
    public function testGetRate()
    {
        $result = Tax::getRate();
        $this->assertEquals(0.10, $result);
    }

    /**
     * 消費税計算テスト
     */
    public function testCalcTax()
    {
        $result = Tax::calcTax(1100);
        $this->assertEquals(100, $result);
    }

    /**
     * 税抜き価格計算テスト
     */
    public function testCalcPriceExcludeTax()
    {
        $result = Tax::calcPriceExcludeTax(1100);
        $this->assertEquals(1000, $result);
    }

    /**
     * 消費税計算テスト (端数あり)
     */
    public function testCalcTax2()
    {
        $result = Tax::calcTax(1101);
        $this->assertEquals(100, $result);
    }

    /**
     * 税抜き価格計算テスト (端数あり)
     */
    public function testCalcPriceExcludeTax2()
    {
        $result = Tax::calcPriceExcludeTax(1101);
        $this->assertEquals(1001, $result);
    }
}
