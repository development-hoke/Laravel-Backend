<?php

use Illuminate\Database\Seeder;

class OrdersAllSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = require __DIR__.'/../../app/HttpCommunication/Ymdy/Mock/fixtures/order_list.php';

        if (!isset($response['orders'])) {
            echo "JSONが不正\n";

            return;
        }

        foreach ($response['orders'] as $data) {
            DB::transaction(function () use ($data) {
                $order = \App\Models\Order::create([
                    'member_id' => $data['member_id'],
                    'order_date' => $data['order_date'],
                    'code' => $data['code'],
                    'payment_type' => $data['payment_type'],
                    'delivery_hope_date' => $data['delivery_hope_date'],
                    'delivery_hope_time' => $data['delivery_hope_time'],
                    'delivery_fee' => $data['delivery_fee'],
                    'price' => $data['price'],
                    'tax' => $data['tax'],
                    'fee' => $data['fee'],
                    'use_point' => $data['use_point'],
                    'order_type' => $data['order_type'],
                    'paid' => $data['paid'],
                    'paid_date' => $data['paid_date'],
                    'add_point' => $data['add_point'],
                    'device_type' => $data['device_type'],
                    'use_point' => $data['use_point'],
                ]);

                foreach ($data['order_details'] as $detail) {
                    $itemDetailId = \App\Models\ItemDetailIdentification::where('jan_code', $detail['jan_code'])->first();
                    $orderDetail = \App\Models\OrderDetail::create([
                        'order_id' => $order->id,
                        'item_detail_id' => $itemDetailId->item_detail_id,
                        'retail_price' => $detail['retail_price'],
                        'sale_type' => $detail['sale_type'],
                        'tax_rate_id' => 1,
                    ]);

                    \App\Models\OrderDetailUnit::create([
                        'order_detail_id' => $orderDetail->id,
                        'item_detail_identification_id' => $itemDetailId->id,
                        'amount' => $detail['amount'],
                    ]);
                }

                foreach ($data['order_addresses'] as $address) {
                    $orderDetail = \App\Models\OrderAddress::create($address + [
                        'order_id' => $order->id,
                    ]);
                }
            });
        }
    }
}
