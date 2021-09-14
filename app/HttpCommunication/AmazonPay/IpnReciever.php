<?php

namespace App\HttpCommunication\AmazonPay;

interface IpnReciever
{
    /**
     * IPN通知の受信処理
     *
     * @param array $input
     *
     * @return array
     */
    public static function revieve(array $input = []);
}
