<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #0f172a; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #dc2626, #991b1b); padding: 36px 40px; text-align: center; }
        .header h1 { color: white; font-size: 22px; font-weight: 800; }
        .header p { color: rgba(255,255,255,.75); font-size: 13px; margin-top: 6px; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 17px; font-weight: 700; margin-bottom: 12px; }
        .intro { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 24px; }
        .creds { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; margin-bottom: 24px; }
        .cred-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .cred-row:last-child { border-bottom: none; }
        .cred-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
        .cred-value { font-size: 13px; font-weight: 700; color: #0f172a; }
        .cta { text-align: center; margin: 24px 0; }
        .cta a { display: inline-block; background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; text-decoration: none; padding: 14px 36px; border-radius: 50px; font-size: 14px; font-weight: 700; }
        .notice { background: #fefce8; border: 1px solid #fde68a; border-radius: 12px; padding: 14px 18px; font-size: 13px; color: #92400e; line-height: 1.6; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; line-height: 1.7; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🎉 Welcome to the Client Portal!</h1>
        <p>Your account has been approved — here are your login details</p>
    </div>
    <div class="body">
        <p class="greeting">Hello, {{ $client->name }}!</p>
        <p class="intro">
            Your internet service application has been approved. You can now access your personal client portal
            to view your billing, make payments, track your service, and submit support requests.
        </p>
        <div class="creds">
            <div class="cred-row">
                <span class="cred-label">Portal URL</span>
                <span class="cred-value">{{ $portalUrl }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Email</span>
                <span class="cred-value">{{ $client->email }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Temporary Password</span>
                <span class="cred-value">{{ $tempPassword }}</span>
            </div>
        </div>
        <div class="cta">
            <a href="{{ $portalUrl }}">Access My Portal →</a>
        </div>
        <div class="notice">
            <strong>⚠ Important:</strong> Please log in and change your password immediately from Profile Settings.
            This temporary password will remain active until you change it.
        </div>
    </div>
    <div class="footer">
        <strong>{{ config('app.name') }}</strong><br>
        This is an automated email. Please do not reply directly to this message.
    </div>
</div>
</body>
</html>
