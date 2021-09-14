<?php

$json = <<<EOF
{
  "division_groups": [
    {
      "id": 1,
      "code": "1",
      "name": "Violet",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    },
    {
      "id": 2,
      "code": "2",
      "name": "Rouge",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    },
    {
      "id": 3,
      "code": "3",
      "name": "nouer",
      "created_at": "2021-01-21 14:34:14",
      "updated_at": "2021-01-21 14:34:14"
    }
  ]
}
EOF;

return json_decode($json, true);
