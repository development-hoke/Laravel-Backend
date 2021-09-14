<?php

namespace App\Utils;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderLog
{
    /**
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    private static function disk()
    {
        return Storage::disk('order');
    }

    /**
     * 未購入時点のログファイルのパス生成
     * storage/logs/orders/unpurchased/{$sesseion_id}.log
     *
     * @param $sessionId
     *
     * @return string
     */
    private static function createUnPurchasedLogPath($sessionId)
    {
        return "/unpurchased/{$sessionId}.log";
    }

    /**
     * 購入後のログファイルパス生成
     * storage/logs/orders/{yyyymmdd}/{$orders.code}.log
     *
     * @param Order $order
     *
     * @return string
     */
    private static function createPurchasedLogPath(Order $order)
    {
        $date = $order->order_date->format('Ymd');

        return "/{$date}/{$order->code}.log";
    }

    /**
     * ログに書き込めるようにテキストに変換
     *
     * @param string $message
     * @param array $params
     *
     * @return string
     */
    private static function toLogText($message = '', array $params = [])
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        if (empty($params)) {
            return "[{$now}] $message";
        } else {
            return "[{$now}] $message\n" . json_encode($params);
        }
    }

    /**
     * 書き込むべきログが見つからなかった場合のエラーを記録
     *
     * @param $path
     * @param string $message
     * @param array $params
     */
    private static function error($path, $message = '', $params = [])
    {
        $data = [
            'path' => $path,
            'message' => $message,
            'params' => $params,
        ];
        Log::error(__('error.invalid_file_path'), $data);
    }

    /**
     * 未購入時点のログ
     *
     * @param $sessionId
     * @param string $message
     * @param array $params
     */
    public static function unPurchased($sessionId, string $message, array $params = [])
    {
        $disk = self::disk();
        $disk->append(self::createUnPurchasedLogPath($sessionId), self::toLogText($message, $params));
    }

    /**
     * 購入後のログに切り替え
     *
     * @param $sessionId
     * @param Order $order
     */
    public static function moveToPurchased($sessionId, Order $order)
    {
        $disk = self::disk();
        $unPurchasedLogPath = self::createUnPurchasedLogPath($sessionId);
        if ($disk->exists($unPurchasedLogPath)) {
            $newLogPath = self::createPurchasedLogPath($order);
            if (!$disk->exists($newLogPath)) {
                $disk->move($unPurchasedLogPath, self::createPurchasedLogPath($order));
            }
        } else {
            self::error($unPurchasedLogPath);
        }
    }

    /**
     * 購入後のログ
     *
     * @param Order $order
     * @param string $message
     * @param array $params
     */
    public static function purchased(Order $order, string $message = '', array $params = [])
    {
        $disk = self::disk();
        $purchasedLogPath = self::createPurchasedLogPath($order);
        $disk->append($purchasedLogPath, self::toLogText($message, $params));
    }
}
