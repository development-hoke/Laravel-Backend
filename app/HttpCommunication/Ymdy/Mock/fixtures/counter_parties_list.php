<?php

$json = <<<EOF
{
  "counter_parties": [
    {
      "id": 1,
      "type": 1,
      "code": "9999",
      "name": "株式会社ヤマダヤ",
      "name_kana": "ヤマダヤ",
      "zip": "1130034",
      "address1": "東京都文京区湯島",
      "address2": "3-14-8 加田湯島ビル5階",
      "tel1": "090",
      "tel2": "1234",
      "bank": "1",
      "bank_branch_id": 1,
      "account_type": "1",
      "account_name": "イトウユウホ",
      "account_number": "123318932",
      "payment_rate": "29",
      "fee_flag": 1,
      "email_order": "admin@example.com",
      "email_accounting": "admin@example.com",
      "url": "admin.example.com"
    }
  ]
}
EOF;

return json_decode($json, true);
