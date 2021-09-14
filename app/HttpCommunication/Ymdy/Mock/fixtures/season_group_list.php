<?php

$json = <<<EOF
{
  "season_groups": [
    {
      "id": 1,
      "code": "0",
      "name": "通年",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    },
    {
      "id": 2,
      "code": "1",
      "name": "秋冬",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    },
    {
      "id": 3,
      "code": "2",
      "name": "春夏",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    }
  ]
}
EOF;

return json_decode($json, true);
