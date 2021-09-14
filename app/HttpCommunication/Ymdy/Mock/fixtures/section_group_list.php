<?php

$json = <<<EOF
{
  "section_groups": [
    {
      "id": 1,
      "name": "トップス"
    },
    {
      "id": 2,
      "name": "ボトムス"
    },
    {
      "id": 3,
      "name": "アウター"
    }
  ]
}
EOF;

return json_decode($json, true);
