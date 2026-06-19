<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #0f172a; }
        .wrapper { max-width: 580px; margin: 32px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); padding: 36px 40px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; margin-top: 12px; }
        .header p { color: rgba(255,255,255,.8); font-size: 13px; margin-top: 6px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 17px; font-weight: 700; margin-bottom: 10px; }
        .text { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 24px; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #4f46e5, #6366f1); color: #fff; text-decoration: none; padding: 14px 40px; border-radius: 50px; font-size: 15px; font-weight: 700; }
        .note { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 16px; line-height: 1.6; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; line-height: 1.7; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <svg width="40" height="40" fill="none" stroke="white" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        <h1>Verify Your Email Address</h1>
        <p>One step to activate your technician account</p>
    </div>
    <div class="body">
        <p class="greeting">Hello, {{ $technician->name }}!</p>
        <p class="text">
            Your technician account has been created on <strong>{{ config('app.name') }}</strong>.
            Please verify your email address to ensure you receive job assignment notifications.
        </p>
        <p class="text">
            Click the button below to verify your email. This link will expire in <strong>24 hours</strong>.
        </p>
        <div class="btn-wrap">
            <a href="{{ url('/technicians/verify-email/' . $technician->email_verification_token) }}" class="btn">
                Verify My Email
            </a>
        </div>
        <p class="note">
            If you did not expect this email, you can safely ignore it.<br>
            If the button doesn't work, copy and paste this link into your browser:<br>
            <span style="color:#4f46e5;word-break:break-all;">{{ url('/technicians/verify-email/' . $technician->email_verification_token) }}</span>
        </p>
    </div>
    <div class="footer">
        <strong>{{ config('app.name') }}</strong><br>
        This is an automated email. Please do not reply directly.
    </div>
</div>
</body>
</html>
