<?php

$json = <<<EOL
{
  "code": 1,
  "item": [
    {
      "cid": "1000000001",
      "comment": "春コードまとめ",
      "coordinate_height": "150",
      "created_at": "2018-04-01 10:00:00",
      "accept_at": "2018-04-01 11:00:00",
      "accept": "1",
      "label_id": "1",
      "user_id": "1000001",
      "user_name": "丸⼭弥奈",
      "user_name_kana": "マルヤマミナ",
      "user_code": "user001",
      "user_image_url": "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/1000001/9999999999_re.jpg",
      "user_height": "170",
      "shop_id": "100001",
      "shop_code": "ec",
      "shop_name": "EC事業部",
      "pv": "0",
      "image_url": "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/9999999999.jpg",
      "sub_image": [
        "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/8888888888.jpg",
        "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/7777777777.jpg"
      ],
      "label_name": "EVRIS",
      "label_code": "evris",
      "label_name_kana": "",
      "products": [
        {
          "cid": "1000000001",
          "product_code": "BOOTS0000001",
          "stock": "0",
          "shop_stock": "0",
          "is_public_sku": "1",
          "is_public": "1",
          "name": "Vanishブーツ",
          "image_url": "https://www.v-standard.com/products/images/BOOTS0000001.jpg",
          "price": "26469",
          "selling_price": "26469",
          "product_url": "https://www.v-standard.com/products/detail/TSHIRTS0000001",
          "base_product_code": "BOOTS000000",
          "category": "ブーツ",
          "category_code": "BOOTS",
          "color": "ブラック",
          "color_code": "0",
          "size": "27.5",
          "size_code": "27.5",
          "label": "VS",
          "label_code": "vs"
        },
        {
          "cid": "1000000001",
          "product_code": "TSHIRTS0000001",
          "stock": "0",
          "shop_stock": "0",
          "is_public_sku": "1",
          "is_public": "1",
          "name": "VanishTシャツ",
          "image_url": "https://www.v-standard.com/products/images/TSHIRTS0000001.jpg",
          "price": "9709",
          "selling_price": "9709",
          "product_url": "https://www.v-standard.com/products/detail/TSHIRTS0000001",
          "base_product_code": "TSHIRTS000000",
          "category": "Tシャツ",
          "category_code": "TSHIRTS",
          "color": "ホワイト",
          "color_code": "9",
          "size": "S",
          "size_code": "3",
          "label": "Vanish",
          "label_code": "vanish"
        }
      ],
      "tags": [
        "ブーツ",
        "Tシャツ"
      ],
      "resized_main_images": [
        {
          "org": "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/9999999999.jpg",
          "l": "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/9999999999_l.jpg",
          "m": "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/9999999999_m.jpg",
          "s": "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/9999999999_s.jpg"
        }
      ],
      "resized_sub_images": [
        {
          "org": [
            "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/8888888888.jpg",
            "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/7777777777.jpg"
          ],
          "l": [
            "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/8888888888_l.jpg",
            "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/7777777777_l.jpg"
          ],
          "m": [
            "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/8888888888_m.jpg",
            "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/7777777777_m.jpg"
          ],
          "s": [
            "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/8888888888_s.jpg",
            "https://s3-ap-northeast-1.amazonaws.com/static.staff-start.com/9999/9/7777777777_s.jpg"
          ]
        }
      ]
    }
  ]
}
EOL;

return json_decode($json, true);
