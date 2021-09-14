<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderDiscount;
use App\Models\OrderDiscountLog;
use App\Models\OrderLog;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->truncateDatabase();
    }

    public function tearDown(): void
    {
        $this->truncateDatabase();
        parent::tearDown();
    }

    public function eachAfter()
    {
        $this->truncateDatabase();
    }

    private function truncateDatabase()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Order::truncate();
        OrderLog::truncate();
        OrderDiscount::truncate();
        OrderDiscountLog::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * @return array
     */
    public function updateWithTimestampProvider()
    {
        return [
            '値の指定があった場合日時が更新される' => [
                ['paid' => 1, 'inspected' => 1, 'deliveryed' => 1], 1,
            ],
            '値の指定がなかった場合日時が更新されない' => [
                ['paid' => null, 'inspected' => null, 'deliveryed' => null], 0,
            ],
        ];
    }

    /**
     * @param array $attributes
     * @param int $expected
     *
     * @return void
     * @dataProvider updateWithTimestampProvider
     */
    public function testUpdateWithTimestamp($attributes, $expected)
    {
        Order::create([
            'id' => 1,
            'member_id' => 200000003,
            'code' => '2021011209967-999-19038',
            'order_date' => '2020-09-10 04:10:51',
            'payment_type' => 2,
            'delivery_type' => 1,
            'delivery_hope_date' => '2020-03-15',
            'delivery_hope_time' => 5,
            'delivery_fee' => 1080,
            'price' => 271399,
            'fee' => 500,
            'use_point' => 2185,
            'order_type' => 3,
            'paid' => 0,
            'paid_date' => null,
            'inspected' => 0,
            'inspected_date' => null,
            'deliveryed' => 0,
            'deliveryed_date' => null,
            'status' => 2,
            'add_point' => 15821,
            'delivery_number' => '16399',
            'delivery_company' => 2,
            'memo1' => 'Accusantium mollitia est et molestias ut ipsam hic magnam accusantium non.',
            'memo2' => 'Qui ea qui ea quae rerum ipsa omnis deserunt.',
            'shop_memo' => 'Qui consequatur dolorem quaerat est non optio deserunt id est ea perferendis.',
            'deleted_at' => null,
            'created_at' => '2021-01-12 20:17:31',
            'updated_at' => '2021-01-12 20:18:21',
            'sale_type' => 1,
        ]);

        $startDatetime = Date::now();

        $o = Order::find(1);

        // 更新させるために毎回何かしら指定する
        $o->delivery_fee = rand(0, 10);

        foreach ($attributes as $name => $value) {
            if (!is_null($value)) {
                $o->{$name} = $value;
            }
        }

        $o->save();

        $actual = Order::where([
            'id' => 1,
            ['paid_date', '>=', $startDatetime->toDateTimeString()],
            ['inspected_date', '>=', $startDatetime->toDateTimeString()],
            ['deliveryed_date', '>=', $startDatetime->toDateTimeString()],
        ])->count();

        $this->assertEquals($expected, $actual);

        $this->eachAfter();
    }

    public function testUpdateWithLog()
    {
        $o = new Order();

        $o->fill([
            'id' => 1,
            'member_id' => 200000003,
            'code' => '2021011209967-999-19038',
            'order_date' => '2020-09-10 04:10:51',
            'payment_type' => 2,
            'delivery_type' => 1,
            'delivery_hope_date' => '2020-03-15',
            'delivery_hope_time' => 5,
            'delivery_fee' => 1080,
            'price' => 271399,
            'fee' => 500,
            'use_point' => 2185,
            'order_type' => 3,
            'paid' => 0,
            'paid_date' => null,
            'inspected' => 0,
            'inspected_date' => null,
            'deliveryed' => 0,
            'deliveryed_date' => null,
            'status' => 2,
            'add_point' => 15821,
            'delivery_number' => '16399',
            'delivery_company' => 2,
            'memo1' => 'Accusantium mollitia est et molestias ut ipsam hic magnam accusantium non.',
            'memo2' => 'Qui ea qui ea quae rerum ipsa omnis deserunt.',
            'shop_memo' => 'Qui consequatur dolorem quaerat est non optio deserunt id est ea perferendis.',
            'deleted_at' => null,
            'created_at' => '2021-01-12 20:17:31',
            'updated_at' => '2021-01-12 20:18:21',
            'sale_type' => 1,
        ]);

        $o->save();

        $logs = OrderLog::where(['order_id' => $o->id]);

        $this->assertEquals(1, $logs->count(), '新規作成時にログが作成される');

        $o->memo1 = 'abcdefg';
        $o->save();

        $logs = OrderLog::where(['order_id' => $o->id])->get();
        $this->assertEquals(2, $logs->count(), '更新時にログが作成される');
        $this->assertEquals('abcdefg', $logs->last()->memo1, '更新時にログが作成される');

        $this->eachAfter();
    }

    public function deliveryFeeDiscoutTestProvider()
    {
        return [
            'クーポン送料割引 + 予約販売送料割引 + 通常送料割引' => [
                [\App\Enums\OrderDiscount\Type::CouponDeliveryFee, \App\Enums\OrderDiscount\Type::ReservationDeliveryFee, \App\Enums\OrderDiscount\Type::DeliveryFee],
                \App\Enums\OrderDiscount\Type::CouponDeliveryFee,
            ],
            '予約販売送料割引 + 通常送料割引' => [
                [\App\Enums\OrderDiscount\Type::ReservationDeliveryFee, \App\Enums\OrderDiscount\Type::DeliveryFee],
                \App\Enums\OrderDiscount\Type::ReservationDeliveryFee,
            ],
        ];
    }

    /**
     * @param array $params
     * @param int $extected
     *
     * @return void
     * @dataProvider deliveryFeeDiscoutTestProvider
     */
    public function testGetDeliveryFeeDiscountTypeAttribute($params, $extected)
    {
        $o = new Order();

        $o->fill([
            'id' => 1,
            'member_id' => 200000003,
            'code' => '2021011209967-999-19038',
            'order_date' => '2020-09-10 04:10:51',
            'payment_type' => 2,
            'delivery_type' => 1,
            'delivery_hope_date' => '2020-03-15',
            'delivery_hope_time' => 5,
            'delivery_fee' => 1080,
            'price' => 271399,
            'fee' => 500,
            'use_point' => 2185,
            'order_type' => 3,
            'paid' => 0,
            'paid_date' => null,
            'inspected' => 0,
            'inspected_date' => null,
            'deliveryed' => 0,
            'deliveryed_date' => null,
            'status' => 2,
            'add_point' => 15821,
            'delivery_number' => '16399',
            'delivery_company' => 2,
            'memo1' => 'Accusantium mollitia est et molestias ut ipsam hic magnam accusantium non.',
            'memo2' => 'Qui ea qui ea quae rerum ipsa omnis deserunt.',
            'shop_memo' => 'Qui consequatur dolorem quaerat est non optio deserunt id est ea perferendis.',
            'deleted_at' => null,
            'created_at' => '2021-01-12 20:17:31',
            'updated_at' => '2021-01-12 20:18:21',
            'sale_type' => 1,
        ]);

        $o->save();

        foreach ($params as $type) {
            OrderDiscount::create([
                'orderable_type' => Order::class,
                'orderable_id' => 1,
                'applied_price' => 1000,
                'type' => $type,
                'method' => \App\Enums\OrderDiscount\Method::Fixed,
                'discount_price' => 1000,
            ]);
        }

        usleep(10000);

        $o->load('deliveryFeeDiscount');

        $this->assertEquals($extected, $o->getDeliveryFeeDiscountTypeAttribute());
    }
}
