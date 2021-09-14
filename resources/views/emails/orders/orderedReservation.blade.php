{{ $order->memberOrderAddress->lname }} {{ $order->memberOrderAddress->fname }}  様<br />
 <br />
 この度はご注文いただき、誠にありがとうございます。<br />
 <br />
 <br />
 ──────────────<br />
 ■ご注文詳細<br />
 [ご注文コード]{{ $order->code }}<br />
 [ご注文日時]{{ $order->order_date->format('Y/m/d H:i') }}<br />
 [お支払い方法]{{ \App\Enums\Order\PaymentType::getDescription($order->payment_type) }}<br />
 [ご請求金額]{{ \App\Utils\Format::yen($order->price) }}<br />
 <br />
 ──────────────<br />
 ■ご注文者様の情報<br />
 [お名前]{{ $order->memberOrderAddress->lname }} {{ $order->memberOrderAddress->fname }}<br />
 [お名前（カナ）]{{ $order->memberOrderAddress->lkana }} {{ $order->memberOrderAddress->fkana }}<br />
 [電話番号]{{ $order->memberOrderAddress->tel }}<br />
 [郵便番号]{{ $order->memberOrderAddress->zip }}<br />
 [住所]{{ (
   $order->memberOrderAddress->pref->name .
   $order->memberOrderAddress->city .
   $order->memberOrderAddress->town .
   $order->memberOrderAddress->city .
   $order->memberOrderAddress->address ?? '' .
   $order->memberOrderAddress->building ?? ''
  ) }}<br />
<br />
 ──────────────<br />
 ■お届け先の情報<br />
 [お名前]{{ $order->deliveryOrderAddress->lname }} {{ $order->deliveryOrderAddress->fname }}<br />
 [お名前（カナ）]{{ $order->deliveryOrderAddress->lkana }} {{ $order->deliveryOrderAddress->fkana }}<br />
 [電話番号]{{ $order->deliveryOrderAddress->tel }}<br />
 [郵便番号]{{ $order->deliveryOrderAddress->zip }}<br />
 [住所]{{ (
  $order->deliveryOrderAddress->pref->name .
  $order->deliveryOrderAddress->city .
  $order->deliveryOrderAddress->town .
  $order->deliveryOrderAddress->city .
  $order->deliveryOrderAddress->address ?? '' .
  $order->deliveryOrderAddress->building ?? ''
 ) }}<br />
<br />
 [配送会社]佐川急便（沖縄県のみゆうパック）<br />
 [お届け指定]{{
  isset($order->delivery_hope_date) && isset($order->delivery_hope_time)
    ? sprintf('%s %s', $order->delivery_hope_date->format('Y/m/d'), \App\Enums\Order\DeliveryTime::getDescription($order->delivery_hope_time))
    : (isset($order->delivery_hope_date)
        ? $order->delivery_hope_date->format('Y/m/d')
        : 'なし')
  }}<br />
<br />
 ──────────────<br />
 ■ご注文内容<br />
 @foreach ($order->orderDetails as $orderDetail)
 -----<br />
 [商品名]{{ $orderDetail->itemDetail->item->display_name }}<br />
 [商品お問い合わせ番号]{{ $orderDetail->itemDetail->item->product_number }}<br />
 [カラー]{{ $orderDetail->itemDetail->color->display_name }}<br />
 [サイズ]{{ $orderDetail->itemDetail->size->name }}<br />
 <br />
 [価格]{{ \App\Utils\Format::yen($orderDetail->ordered_item_price) }}<br />
 [数量]{{ $orderDetail->amount }}<br />
 [金額]{{ \App\Utils\Format::yen($orderDetail->total_ordered_item_price) }}<br />
 @endforeach
 <br />
 ---------------<br />
 [商品合計金額]{{ \App\Utils\Format::yen($order->orderDetails->sum('total_ordered_item_price')) }}<br />
 [キャンペーン割引]{{ \App\Utils\Format::yen($order->orderDetails->sum('total_event_discount_price')) }}<br />
 [クーポン利用]{{ \App\Utils\Format::yen($order->orderUsedCoupons->sum('item_applied_price')) }}<br />
 [ポイント利用]{{ $order->use_point }}pt<br />
 [送料]{{ \App\Utils\Format::yen($order->delivery_fee) }}<br />
 [決済手数料]{{ \App\Utils\Format::yen($order->fee) }}<br />
 [ご請求金額]{{ \App\Utils\Format::yen($order->price) }}<br />
 <br />
 ──────────────<br />
 ■ご要望<br />
 [ご要望内容]{{ isset($message['type']) ? \App\Enums\Order\Request::getDescription($message['type']) : '' }}<br />
 [ご要望本文]<br />
 {{ $message['content'] ?? '' }}<br />
 <br />
 ──────────────<br />
 <br />
 <br />
 ・予約商品は通常の発送日程とは異なります。<br />
 ・発送予定日は、商品ページでご確認いただければと存じます。<br />
 ・予約商品が当店に入荷しましたら、メールにてご連絡いたします。<br />
 <br />
 大変お待たせして申し訳ございませんが、<br />
 もうしばらくお待ちいただければと存じます。<br />
