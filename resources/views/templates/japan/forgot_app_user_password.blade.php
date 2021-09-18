<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>【シェアシテ】パスワード再設定の申請</title>
</head>
<body>
    <div style="margin: 5px 0;">
        <p>この度は、シェアシテをご利用いただき、</p>
        <p>誠にありがとうございます。</p>
    </div>
    <div style="margin: 5px 0;">
        <p>本日{{$timeNow['hour']}}時{{$timeNow['minute']}}分{{$timeNow['second']}}秒にパスワード再設定の申請を受け付けました。</p>
        <p>下記URLを開いて、パスワードの再設定を行ってください。</p>
    </div>
    <div style="margin: 10px 0;">
        <p>【パスワード再設定URL】</p>
        <a href="{{$url}}" style="margin-right: 30px;">{{$url}}</a>
    </div>
    <div style="margin: 5px 0;">
        <p>3時間以内にパスワードの再設定を完了されない場合は申請がキャンセル</p>
        <p>されますのでご注意ください。キャンセルされた場合は、もう一度申請を</p>
        <p>行ってください。</p>
    </div>
    <div style="margin: 5px 0;">
        <p>何かご不明な点がございましたら、お気軽にお問い合せください。</p>
        <p>今後ともどうぞよろしくお願いいたします。</p>
    </div>
    <p style="margin: 5px 0;">──────────────────────────────────</p>
    <div style="margin: 10px 0;">
        <p>▽問い合わせに関しましては、お問い合わせフォームよりご連絡下さい。</p>
        <p>
            <a href="https://d3ugk0uitg9c06.cloudfront.net/main-menu/contact">https://d3ugk0uitg9c06.cloudfront.net/main-menu/contact</a>
        </p>
    </div>
    <div style="margin: 5px 0;">
        <p>シェアシテ<a href="https://shareshite.com" style="margin-left: 10px">  https://shareshite.com</a></p>
    </div>
    <p style="margin: 5px 0;">──────────────────────────────────</p>
    <div>
        <p>機密情報に関する注意事項：</p>
        <p>このメールは、発信者が意図した受信者のみが利用することを意図したものです。</p>
        <p>万が一、貴殿がこのメールの発信者が意図した受信者でない場合には、</p>
        <p>直ちに送信者への連絡とこのメールを破棄願います。</p>
    </div>
</body>
</html>
