<?php

$json = <<<EOL
{
  "purchasing_bill": {
    "id": 1,
    "member_id": 100000000,
    "shop_id": 1,
    "shop": {
      "id": 1,
      "name": "string",
      "created_at": "2020-01-01 09:00:00",
      "updated_at": "2020-01-01 09:00:00"
    },
    "point_usage_id": 1,
    "point_usage": {
      "id": 1,
      "member_id": 100000000,
      "shop_id": 1,
      "shop": {
        "id": 1,
        "name": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "point_adjustment_reason_id": 1,
      "point_adjustment_reason": {
        "id": 1,
        "reason": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "amount": 0,
      "is_valid": true,
      "remark": "string",
      "created_at": "2020-01-01 09:00:00",
      "updated_at": "2020-01-01 09:00:00"
    },
    "point_gain_id": 1,
    "point_gain": {
      "id": 1,
      "member_id": 100000000,
      "shop_id": 1,
      "shop": {
        "id": 1,
        "name": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "point_adjustment_reason_id": 1,
      "point_adjustment_reason": {
        "id": 1,
        "reason": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "amount": 0,
      "is_valid": true,
      "remark": "string",
      "created_at": "2020-01-01 09:00:00",
      "updated_at": "2020-01-01 09:00:00"
    },
    "staff_cd": "string",
    "bill_number": "string",
    "total_price": 0,
    "total_price_no_tax": 0,
    "event_cd": "string",
    "date": "2020-01-01 09:00:00",
    "pos_number": "string",
    "created_at": "2020-01-01 09:00:00",
    "updated_at": "2020-01-01 09:00:00",
    "gain_point_log": {
      "id": 1,
      "member_id": 100000000,
      "shop_id": 1,
      "shop": {
        "id": 1,
        "name": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "point_adjustment_reason_id": 1,
      "point_adjustment_reason": {
        "id": 1,
        "reason": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "amount": 0,
      "is_valid": true,
      "remark": "string",
      "created_at": "2020-01-01 09:00:00",
      "updated_at": "2020-01-01 09:00:00"
    },
    "usage_point_log": {
      "id": 1,
      "member_id": 100000000,
      "shop_id": 1,
      "shop": {
        "id": 1,
        "name": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "point_adjustment_reason_id": 1,
      "point_adjustment_reason": {
        "id": 1,
        "reason": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "amount": 0,
      "is_valid": true,
      "remark": "string",
      "created_at": "2020-01-01 09:00:00",
      "updated_at": "2020-01-01 09:00:00"
    },
    "purchasing_bill_items": [
      {
        "id": 1,
        "item_id": 1,
        "bill_id": 1,
        "unit_price": 0,
        "sales_num": 0,
        "tax": 0,
        "not_item_sales_flag": true,
        "pb_div": 1,
        "tax_rate": 0,
        "tax_type": 1,
        "point_rate": 0,
        "point_amount": 0,
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00",
        "deleted_at": "2020-01-01 09:00:00"
      }
    ],
    "purchasing_bill_coupons": [
      {
        "id": 1,
        "member_group_id": 0,
        "name": "string",
        "target_member_type": 1,
        "member_data": [
          0
        ],
        "target_shop_type": 1,
        "shop_data": [
          0
        ],
        "issuance_limit": 0,
        "usage_number_limit": 0,
        "image_path": "string",
        "start_dt": "2020-01-01 09:00:00",
        "end_dt": "2020-01-01 09:00:00",
        "free_shipping_flag": false,
        "discount_item_flag": false,
        "discount_type": 1,
        "discount_amount": 0,
        "discount_rate": 0,
        "target_item_type": 1,
        "item_data": [
          0
        ],
        "usage_amount_term_flag": false,
        "usage_amount_minimum": 0,
        "usage_amount_maximum": 0,
        "is_combinable": false,
        "description": "string",
        "approval_status": 1,
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      }
    ]
  }
}
EOL;

return json_decode($json, true);
