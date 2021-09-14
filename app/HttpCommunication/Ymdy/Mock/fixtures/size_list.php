<?php

$json = <<<EOF
{
  "sizes": [
    {
      "id": 1,
      "code": "7",
      "name": "7号",
      "search_code": "07",
      "created_at": "2021-01-21 14:33:59",
      "updated_at": "2021-01-21 14:33:59"
    },
    {
      "id": 2,
      "code": "9",
      "name": "9号",
      "search_code": "09",
      "created_at": "2021-01-21 14:33:59",
      "updated_at": "2021-01-21 14:33:59"
    },
    {
      "id": 3,
      "code": "1",
      "name": "11号",
      "search_code": "11",
      "created_at": "2021-01-21 14:33:59",
      "updated_at": "2021-01-21 14:33:59"
    },
    {
      "id": 4,
      "code": "3",
      "name": "13号",
      "search_code": "13",
      "created_at": "2021-01-21 14:33:59",
      "updated_at": "2021-01-21 14:33:59"
    },
    {
      "id": 5,
      "code": "0",
      "name": "フリー",
      "search_code": "00",
      "created_at": "2021-01-21 14:33:59",
      "updated_at": "2021-01-21 14:33:59"
    },
    {
      "id": 6,
      "code": "",
      "name": "07(23.0)",
      "search_code": "",
      "created_at": "2021-01-21 14:33:59",
      "updated_at": "2021-01-21 14:33:59"
    },
    {
      "id": 7,
      "code": "",
      "name": "09(23.5)",
      "search_code": "",
      "created_at": "2021-01-21 14:33:59",
      "updated_at": "2021-01-21 14:33:59"
    },
    {
      "id": 8,
      "code": "",
      "name": "11(24.0)",
      "search_code": "",
      "created_at": "2021-01-21 14:33:59",
      "updated_at": "2021-01-21 14:33:59"
    },
    {
      "id": 9,
      "code": "",
      "name": "13(24.5)",
      "search_code": "",
      "created_at": "2021-01-21 14:33:59",
      "updated_at": "2021-01-21 14:33:59"
    }
  ]
}
EOF;

return json_decode($json, true);
