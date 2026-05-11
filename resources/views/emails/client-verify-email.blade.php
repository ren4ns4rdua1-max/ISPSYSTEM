<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #0f172a; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); padding: 40px 40px 32px; text-align: center; }
        .header-icon { width: 64px; height: 64px; background: rgba(255,255,255,.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .header h1 { color: #ffffff; font-size: 24px; font-weight: 800; }
        .header p { color: rgba(255,255,255,.8); font-size: 14px; margin-top: 6px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        .intro { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 28px; }
        .cta { text-align: center; margin: 28px 0; }
        .cta-btn { display: inline-block; background: linear-gradient(135deg, #dc2626, #b91c1c); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 50px; font-size: 15px; font-weight: 700; letter-spacing: .3px; }
        .notice { background: #fefce8; border: 1px solid #fde68a; border-radius: 12px; padding: 16px 20px; margin-top: 24px; font-size: 13px; color: #92400e; line-height: 1.6; }
        .url-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-top: 16px; font-size: 12px; color: #64748b; word-break: break-all; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #94a3b8; line-height: 1.7; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="header-icon">
            <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h1>Verify Your Email Address</h1>
        <p>One quick step before your application is reviewed</p>
    </div>

    <div class="body">
        <p class="greeting">Hello, {{ $client->name }}!</p>
        <p class="intro">
            Thank you for applying for internet service. To ensure we can reach you with updates about your application,
            please verify your email address by clicking the button below.
        </p>

        <div class="cta">
            <a href="{{ $verifyUrl }}" class="cta-btn">✓ Verify My Email</a>
        </div>

        <div class="notice">
            <strong>⏰ This link expires in 24 hours.</strong><br>
            If you did not submit an application, you can safely ignore this email.
        </div>

        <div class="url-box">
            If the button doesn't work, copy and paste this link into your browser:<br>
            {{ $verifyUrl }}
        </div>
    </div>

    <div class="footer">
        <p>
            <strong>{{ config('app.name') }}</strong><br>
            This is an automated email. Please do not reply directly to this message.
        </p>
    </div>
</div>
</body>
</html>
