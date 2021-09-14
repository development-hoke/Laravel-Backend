<?php

$json = <<<EOF
{
  "seasons": [
    {
      "id": 1,
      "group_id": 1,
      "name": "通年",
      "code": "0",
      "mark": "Q",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    },
    {
      "id": 2,
      "group_id": 2,
      "name": "秋",
      "code": "1",
      "mark": "F",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    },
    {
      "id": 3,
      "group_id": 2,
      "name": "冬",
      "code": "2",
      "mark": "p",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    },
    {
      "id": 4,
      "group_id": 2,
      "name": "梅春",
      "code": "3",
      "mark": "U",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    },
    {
      "id": 5,
      "group_id": 3,
      "name": "春",
      "code": "4",
      "mark": "A",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    },
    {
      "id": 6,
      "group_id": 3,
      "name": "夏",
      "code": "5",
      "mark": "C",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    },
    {
      "id": 7,
      "group_id": 3,
      "name": "盛夏",
      "code": "6",
      "mark": "S",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    }
  ]
}
EOF;

return json_decode($json, true);
