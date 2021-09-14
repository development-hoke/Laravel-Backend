<?php

namespace App\HttpCommunication\AmazonPay\Concrete;

use AmazonPay\IpnHandler;
use App\HttpCommunication\AmazonPay\Exceptions\InvalidIpnMessageException;
use App\HttpCommunication\AmazonPay\IpnReciever as IpnRecieverInterface;

class IpnReciever implements IpnRecieverInterface
{
    /**
     * IPN通知の受信処理
     *
     * @param array $input
     *
     * @return array
     */
    public static function revieve(array $input = [])
    {
        try {
            $headers = $input['headers'] ?? getallheaders();
            $body = $input['body'] ?? file_get_contents('php://input');

            $data = (new IpnHandler($headers, $body))->toArray();

            // 異なるSellerIdのメッセージが来る可能性はないかもしれないが、念の為に検証する。
            if ($data['SellerId'] !== config('http_communication.amazon_pay.merchant_id')) {
                throw new InvalidIpnMessageException();
            }

            return $data;
        } catch (\Exception $e) {
            throw new InvalidIpnMessageException(error_format('error.amazon_pay_invalid_ipn'), null, $e);
        }
    }
}
