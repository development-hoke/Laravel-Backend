<?php

$json = <<<EOF
{
  "point_logs": [
    {
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
    }
  ],
  "total_count": 0
}
EOF;

return json_decode($json, true);
