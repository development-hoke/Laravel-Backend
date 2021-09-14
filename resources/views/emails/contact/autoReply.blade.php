<h1> 【YAMADAYA online store】お問い合わせを受付いたしました。</h1>

<div>
──────────────<br>
このメールは送信専用アドレスから配信されております。<br>
ご返信いただいても、お答えできませんので予めご了承下さい。<br>
──────────────<br>
<br>
この度はYAMADAYA online storeにお問い合わせいただき<br>
誠にありがとうございます。<br>
<br>
<br>
カスタマーサービスのスタッフから<br>
メールアドレス、又は電話番号にご連絡をさせていただきます。<br>
<br>
◆お問い合わせが混雑している場合や、お問い合わせ内容によりましてはご対応までに<br>
お日にち、お時間をいただく場合がございます。<br>
ご迷惑をお掛け致しますが、ご理解、ご了承いただきますようお願い申し上げます。<br>
<br>
<br>
以下、お問い合わせいただきました内容です。<br>
-----<br>
【ご氏名】<br>
{{ $lastName }} {{ $firstName }}<br>
【フリガナ】<br>
{{ $lastNameKana }} {{ $firstNameKana }}<br>
【お電話番号】<br>
{{ $firstNameKana }}<br>
【メールアドレス】<br>
{{ $email }}<br>
【お問い合わせ内容】<br>
{{ \App\Enums\Contact\Type::getDescription($type) }}<br>
【お問い合わせ本文】
<div style="white-space: pre-line;">{!! $content !!}</div>
</div>