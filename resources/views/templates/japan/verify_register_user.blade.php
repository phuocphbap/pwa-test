<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <div>
            <p>{{$userName}}様、この度はシェアシテへご登録いただきありがとうございます。</p>
            <p>メールアドレスの確認を行いますので下記のボタンをクリックして登録を完了してください。</p>
        </div>
        <div style="margin: 10px 0;">
            <p>アドレス確認の有効期限は、{{$exprireTime}} です。  </p>
        </div>

        <div style="margin: 10px 0;">
            <a href="{{$url}}" style="background:#15c;text-decoration:none !important; font-weight:bold; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;">
            アドレス確認
            </a>
        </div>

        <p style="margin: 5px 0;">──────────────────────────────────</p>
        
        <div style="margin: 10px 0;">
            <p>▽問い合わせに関しましては、お問い合わせフォームよりご連絡下さい。</p>
            <p><a href="https://d3ugk0uitg9c06.cloudfront.net/main-menu/contact">https://d3ugk0uitg9c06.cloudfront.net/main-menu/contact</a></p>
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