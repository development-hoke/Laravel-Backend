<?php

/*
payment_type:
    const Bank = 1;
    const Cod = 2;
    const CreditCard = 3;
    const NP = 4;
    const AmazonPay = 5;
delivery_hope_time:
    const Am = 1;
    const Time1 = 2;
    const Time2 = 3;
    const Time3 = 4;
    const Time4 = 5;
order_type:
    const Normal = 1;
    const Reserve = 2;
    const BackOrder = 3;
paid:
    1: 入金済み / 0: 未入金
device_type:
    1: PC 2: SP
order_details.sale_type:
    const Sale = 1; B
    const Employee = 2; P
order_address.type:
    const Member = 1;
    const Delivery = 2;
    const Bill = 3;
*/

$json = <<<EOF
{
  "orders": [
    {
      "member_id": "200000035",
      "order_date": "2021-02-18 18:00:00",
      "code": "20210218-00005-123-00001",
      "payment_type": 1,
      "delivery_hope_date": "2021-02-18",
      "delivery_hope_time": 1,
      "delivery_fee": 210,
      "price": 10000,
      "tax": 100,
      "fee": 100,
      "use_point": 100,
      "order_type": 1,
      "paid": 1,
      "paid_date": "2021-02-18 18:00:00",
      "add_point": 100,
      "device_type": 1,
      "order_details": [
        {
            "jan_code": "0011000100379",
            "amount": 1,
            "sale_type": 2,
            "retail_price": 1100
        },
        {
            "jan_code": "0011000100331",
            "amount": 1,
            "sale_type": 1,
            "retail_price": 1200
        }
      ],
      "order_addresses": [
        {
          "type": "1",
          "fname": "ミズ",
          "lname": "テスト",
          "fkana": "ミズ",
          "lkana": "テスト",
          "tel": "03123456778",
          "pref_id": "13",
          "zip": "1030014",
          "city": "中央区",
          "town": "日本橋蛎殻町",
          "address": "1-2-3",
          "building": "テストビル",
          "email": "mizu@test.mail"
        },
        {
          "type": "2",
          "fname": "ミズ",
          "lname": "テスト",
          "fkana": "ミズ",
          "lkana": "テスト",
          "tel": "03123456778",
          "pref_id": "13",
          "zip": "1030014",
          "city": "中央区",
          "town": "日本橋蛎殻町",
          "address": "1-2-3",
          "building": "テストビル",
          "email": "mizu@test.mail"
        },
        {
          "type": "3",
          "fname": "ミズ",
          "lname": "テスト",
          "fkana": "ミズ",
          "lkana": "テスト",
          "tel": "03123456778",
          "pref_id": "13",
          "zip": "1030014",
          "city": "中央区",
          "town": "日本橋蛎殻町",
          "address": "1-2-3",
          "building": "テストビル",
          "email": "mizu@test.mail"
        }
      ]
    }
  ]
}
EOF;

return json_decode($json, true);
