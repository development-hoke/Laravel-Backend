<?php

use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

/**
 *  rollbackしたときなど、migration:freshがテスト時に必要な時に実効する。
 */
class MigrateFresh extends TestCase
{
    use CreatesApplication;

    /**
     * @test
     */
    public function forceMigrateFresh()
    {
        $this->artisan('migrate:fresh');
        $this->assertTrue(true);
    }
}
