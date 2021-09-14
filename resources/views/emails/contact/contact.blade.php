<h1> 【YAMADAYA】お客様からのお問い合わせ</h1>

<div>
-----<br>
【ご氏名】<br>
{{ $lastName }} {{ $firstName }}<br>
【フリガナ】<br>
{{ $lastNameKana }} {{ $firstNameKana }}<br>
【お電話番号】<br>
{{ $phone }}<br>
【メールアドレス】<br>
{{ $email }}<br>
【お問い合わせ内容】<br>
{{ \App\Enums\Contact\Type::getDescription($type) }}<br>
【お問い合わせ本文】
<div style="white-space: pre-line;">{!! $content !!}</div>
</div>