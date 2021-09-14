<?php

$json = <<<EOF
{
  "sections": [
    {
      "id": 1,
      "group_id": 1,
      "code": 11,
      "name": "セーター",
      "mark": "A",
      "abbreviation": "A"
    },
    {
      "id": 2,
      "group_id": 1,
      "code": 12,
      "name": "カットソー",
      "mark": "T",
      "abbreviation": "T"
    },
    {
      "id": 3,
      "group_id": 1,
      "code": 13,
      "name": "ブラウス",
      "mark": "B",
      "abbreviation": "B"
    },
    {
      "id": 4,
      "group_id": 2,
      "code": 21,
      "name": "スカート",
      "mark": "H",
      "abbreviation": "H"
    }
  ]
}
EOF;

return json_decode($json, true);
