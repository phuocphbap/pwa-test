<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Verify Your Email Address</h2>

        <div>
            <p>Dear {{$userName}},</p>
            <p>Thanks for creating an account.</p>
            <p>Please click button below to verify your email address.</p>
        </div>
        <div style="margin 20px 0;">
            <a href="{{$url}}" style="background:#15c;text-decoration:none !important; font-weight:bold; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;">
                Verify account
            </a>
        </div>
        <div>
            <p>If you have any questions please feel free to contact us.</p>
        </div>

    </body>
</html>