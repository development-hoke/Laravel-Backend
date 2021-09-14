<?php

$json = <<<EOL
{
  "member_token": {
    "token": "aaaaaaaaaaaaaaaaaaa",
    "member_id": 100000000,
    "limit": "2020-01-01 09:00:00",
    "member": {
      "id": 100000000,
      "card_id": 100000000,
      "area_id": 1,
      "area": {
        "id": 1,
        "name": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "shop_id": 1,
      "shop": {
        "id": 1,
        "name": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "pref_id": 1,
      "pref": {
        "id": 1,
        "name": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "rank_id": 1,
      "rank": {
        "id": 1,
        "name": "string",
        "created_at": "2020-01-01 09:00:00",
        "updated_at": "2020-01-01 09:00:00"
      },
      "cp_member_id": "string",
      "fname": "string",
      "lname": "string",
      "fkana": "string",
      "lkana": "string",
      "birthday": "2020-01-01",
      "tel": "0300000000",
      "mb_tel": "09000000000",
      "gender": 3,
      "zip": "1638001",
      "city": "string",
      "town": "string",
      "address": "string",
      "building": "string",
      "email": "user@example.com",
      "pc_mail": "user@example.com",
      "mb_mail": "user@example.com",
      "post_dm": true,
      "mail_dm": "1",
      "black_status": 0,
      "remark1": "string",
      "remark2": "string",
      "remark3": "string",
      "memo": "string",
      "temp_admission_at": "2020-01-01 09:00:00",
      "guest_admission_at": "2020-01-01 09:00:00",
      "admission_at": "2020-01-01 09:00:00",
      "transferred_flag": true,
      "mail_authed_at": "2020-01-01 09:00:00",
      "amazon_association": true,
      "amazon_user_id": "string",
      "amazon_token": "string",
      "leave_reason": 1,
      "pre_registered_at": "2020-01-01 09:00:00",
      "old_member_flag": false,
      "fregi_token": "string",
      "staff_code": "string",
      "leave_masked_flag": false,
      "failed_at": "2020-01-01 09:00:00",
      "failure_count": 0,
      "locked_at": "2020-01-01 09:00:00",
      "_is_locked": false,
      "created_at": "2020-01-01 09:00:00",
      "updated_at": "2020-01-01 09:00:00",
      "deleted_at": "2020-01-01 09:00:00"
    },
    "created_at": "2020-01-01 09:00:00",
    "updated_at": "2020-01-01 09:00:00"
  }
}
EOL;

return json_decode($json, true);
