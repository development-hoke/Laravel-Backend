<br>
@foreach($itemDetailIdentifications as $itemDetailIdentification)
-------------------- <br>
商品名：{{$itemDetailIdentification->itemDetail->item->name}} <br>
メーカー品番：{{$itemDetailIdentification->itemDetail->item->maker_product_number}} <br>
 JANコード：{{$itemDetailIdentification->jan_code}} <br>
 URL： {{env('ADMIN_APP_URL')}}/item/{{$itemDetailIdentification->itemDetail->item->id}}<br>
@endforeach
 <br>
 が滞留しています。