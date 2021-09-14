<br>
{{$fname}} {{$lname}} 様 <br>
<br>
大変お待たせいたしました。 <br>
 ご注文の商品の発送が完了いたしました。 <br>
 <br>
 到着まで、もうしばらくお待ちいただければと存じます。 <br>
 この度はご注文、誠にありがとうございました。 <br>
 <br>
 ・商品到着後は、お早めに商品をご確認いただきますようお願いいたします。 <br>
 ・商品に不良があった際は1週間以内にご連絡ください。<br>
 <br>
 <br>
 【お届け指定】<br>
 {{$order->delivery_hope_date_description}} <br>
 {{$order->delivery_hope_time_description}} <br>
 <br>
 【お問い合わせ送り状番号】 <br>
 {{$order->delivery_number}} <br>
 <br>
 【配送会社】 <br>
 {{\App\Enums\Order\DeliveryCompany::getDescription($order->delivery_company)}} <br>
 <br>
 【配送状況】 <br>
 明日以降に、各配送会社のホームページでご確認いただけます。 <br>
 @if($order->delivery_company == \App\Enums\Order\DeliveryCompany::Sagawa)
 <a href="https://k2k.sagawa-exp.co.jp/p/sagawa/web/okurijoinput.jsp" target="_blank">{{\App\Enums\Order\DeliveryCompany::getDescription($order->delivery_company)}}</a>
 @else
 <a href=" https://www.post.japanpost.jp/index.html" target="_blank">{{\App\Enums\Order\DeliveryCompany::getDescription($order->delivery_company)}}</a>
 @endif
 <br>
 <br>
 もしうまく遷移できない場合は、下記リンクより遷移し、送り状番号を入力してください。 <br>
 <br>
 @if($order->delivery_company == \App\Enums\Order\DeliveryCompany::Sagawa)
 佐川急便 <br>
 https://k2k.sagawa-exp.co.jp/p/sagawa/web/okurijoresult.jsp <br>
 @else
 ゆうパック <br>
 https://trackings.post.japanpost.jp/services/srv/search/input <br>
 @endif
 <br>
 <br>
 ────────────── <br>
 ■ご注文詳細 <br>
 [ご注文コード] {{$order->code}} <br>
 [ご注文日時] {{date('n月j日', strtotime($order->order_date))}} <br>
 [お支払い方法] {{\App\Enums\Order\PaymentType::getDescription($order->payment_type)}} <br>
 [ご請求金額] {{$order->price}} <br>
 <br>
 ────────────── <br>
 ■ご注文者様の情報 <br>
 [お名前] {{$order->memberOrderAddress->fname}} {{$order->memberOrderAddress->lname}}<br>
 [お名前（カナ）] {{$order->memberOrderAddress->fkana}} {{$order->memberOrderAddress->lkana}} <br>
 [電話番号] {{$order->memberOrderAddress->tel}} <br>
 [郵便番号] {{$order->memberOrderAddress->zip}} <br>
 [住所] {{$order->memberOrderAddress->town}} {{$order->memberOrderAddress->city}} {{$order->memberOrderAddress->address}} {{$order->memberOrderAddress->building}} <br>
 <br>
 ────────────── <br>
 ■お届け先の情報 <br>
 [お名前] {{$order->deliveryOrderAddress->fname}} {{$order->deliveryOrderAddress->lname}} <br>
 [お名前（カナ）] {{$order->deliveryOrderAddress->fkana}} {{$order->deliveryOrderAddress->lkana}} <br>
 [電話番号] {{$order->deliveryOrderAddress->tel}} <br>
 [郵便番号] {{$order->deliveryOrderAddress->zip}} <br>
 [住所] {{$order->deliveryOrderAddress->town}} {{$order->deliveryOrderAddress->city}} {{$order->deliveryOrderAddress->address}} {{$order->deliveryOrderAddress->building}} <br>
 <br>
 [配送会社] {{\App\Enums\Order\DeliveryCompany::getDescription($order->delivery_company)}} <br>
 [お問い合わせ送り状番号] {{$order->delivery_number}} <br>
 [お届け指定]  {{$order->delivery_hope_date_description}} {{$order->delivery_hope_time_description}} <br>
 <br>
 ────────────── <br>
 ■ご注文内容 <br>
 @foreach($orderDetails as $orderDetail)
 ----- <br>
 [商品名] {{$orderDetail->itemDetail->item->name}} <br>
 [商品お問い合わせ番号] {{$orderDetail->itemDetail->item->product_number}} <br>
 [カラー] {{$orderDetail->itemDetail->color->name}} <br>
 [サイズ] {{$orderDetail->itemDetail->size->name}} <br>
 <br>
 [価格] {{$orderDetail->retail_price}} <br>
 [数量] {{$orderDetail->amount}} <br>
 [金額] {{$orderDetail->totalRetailPrice}} <br>
 <br>
 @endforeach

 --------------- <br>
 [商品合計金額] {{$order->price}} <br>
 [キャンペーン割引] {{$order->campaignDiscount}} <br>
 [クーポン利用] {{$order->couponDiscount}} <br>
 [ポイント利用] {{$order->use_point}}pt <br>
 [送料] {{$order->delivery_fee}} <br>
 [決済手数料] {{$order->fee}} <br>
 [ご請求金額] {{$order->price}} <br>
 <br>
 ────────────── <br>
 ■ご要望 <br>
 [ご要望内容] {!! nl2br(e($order->memo1)) !!} <br>
 [ご要望本文] <br>
 {!! nl2br(e($order->memo2)) !!} <br>
 <br>
 ────────────── <br>
 <br>
 <br>
 <br>
 ■NP後払いでお支払いの方へ <br>
 ・お振込用紙を別口で郵送いたします。 <br>
 ・お振込用紙の発行は発送が完了した翌日（土日祝を除く）に行われます。 <br>
 ・郵便事情により、到着まで1週間ほどかかる場合がございます。 <br>
 <br>
 ■発送状況について <br>
 ・「荷物が届かない。」 「お届け指定日時を変更したい。」 「宅配BOXへの投函してほしい。」等のお問い合わせは、 <br>
 大変お手数ですが、お客様ご自身で運送会社にご連絡ください。 <br>
 ・お荷物の保管期限は初回配達日から7日間でございます（※例外あり） 。 <br>
 もし保管期限 までにお受け取りが難しい場合は、お客様ご自身で運送会社にご連絡ください。 <br>
 <br>
 <br>
 ※このメールは送信専用アドレスから配信されております。ご返信いただいても、お答えできませんので予めご了承下さい。 <br>
