<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function doPrivateMethod($instance, $method, $param)
    {
        // ReflectionClassをテスト対象のクラスをもとに作る.
        $reflection = new \ReflectionClass($instance);
        // メソッドを取得する.
        $method = $reflection->getMethod($method);
        // アクセス許可をする.
        $method->setAccessible(true);
        // メソッドを実行して返却値をそのまま返す.
        return $method->invoke($instance, $param);
    }
}
