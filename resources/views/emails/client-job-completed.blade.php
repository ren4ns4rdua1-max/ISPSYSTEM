<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Service is Active</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #0f172a; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #059669 0%, #047857 100%); padding: 40px 40px 32px; text-align: center; }
        .header-icon { width: 68px; height: 68px; background: rgba(255,255,255,.18); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .header h1 { color: #fff; font-size: 24px; font-weight: 800; letter-spacing: -.3px; }
        .header p { color: rgba(255,255,255,.8); font-size: 14px; margin-top: 6px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 10px; }
        .intro { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 28px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: 12px; }
        .info-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 9px 0; border-bottom: 1px solid #f1f5f9; }
        .info-row:last-child { border-bottom: none; padding-bottom: 0; }
        .info-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .info-value { font-size: 13px; color: #1e293b; font-weight: 700; text-align: right; }
        .creds-card { background: linear-gradient(135deg, #0f172a, #1e293b); border-radius: 14px; padding: 22px; margin-bottom: 20px; }
        .creds-card .creds-title { color: rgba(255,255,255,.5); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; margin-bottom: 14px; }
        .creds-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,.07); }
        .creds-row:last-child { border-bottom: none; }
        .creds-label { font-size: 12px; color: rgba(255,255,255,.45); font-weight: 600; }
        .creds-value { font-size: 13px; color: #fff; font-weight: 700; font-family: monospace; letter-spacing: .03em; }
        .tech-card { background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; border-radius: 14px; padding: 18px; margin-bottom: 20px; }
        .tech-name { font-size: 15px; font-weight: 800; color: #1d4ed8; margin-bottom: 4px; }
        .tech-detail { font-size: 13px; color: #3b82f6; margin-top: 3px; }
        .cta { text-align: center; margin: 28px 0; }
        .cta-btn { display: inline-block; background: linear-gradient(135deg, #059669, #047857); color: #fff; text-decoration: none; padding: 15px 40px; border-radius: 50px; font-size: 15px; font-weight: 700; letter-spacing: .3px; }
        .notice { background: #fefce8; border: 1px solid #fde68a; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; font-size: 13px; color: #92400e; line-height: 1.6; }
        .divider { height: 1px; background: #f1f5f9; margin: 24px 0; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #94a3b8; line-height: 1.7; }
        .badge-active { display: inline-block; background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 700; padding: 3px 12px; border-radius: 50px; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Header -->
    <div class="header">
        <div class="header-icon">
            <svg width="34" height="34" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1>Your Internet is Now Active! 🎉</h1>
        <p>Installation complete — you're all set to browse</p>
    </div>

    <!-- Body -->
    <div class="body">
        <p class="greeting">Hello, {{ $client->name }}!</p>
        <p class="intro">
            Great news! Your internet service installation has been completed by our technician.
            Your connection is now <strong>active</strong>. Below are your complete account details and portal login credentials.
        </p>

        <!-- Account Status -->
        <p class="section-title">📋 Account Information</p>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Account #</span>
                <span class="info-value">#{{ str_pad($client->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Full Name</span>
                <span class="info-value">{{ $client->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $client->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone</span>
                <span class="info-value">{{ $client->phone_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Barangay</span>
                <span class="info-value">{{ $client->barangay }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value"><span class="badge-active">✓ Active</span></span>
            </div>
        </div>

        <!-- Subscription -->
        <p class="section-title">📡 Subscription Details</p>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Plan</span>
                <span class="info-value">{{ $client->plan_description }}</span>
            </div>
            @if($client->subscriptionRate)
            <div class="info-row">
                <span class="info-label">Speed</span>
                <span class="info-value">{{ $client->subscriptionRate->speed }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Monthly Fee</span>
                <span class="info-value">₱{{ number_format($client->subscriptionRate->monthly_fee, 2) }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">PPPoE Username</span>
                <span class="info-value">{{ $client->pppoe_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NAP Box</span>
                <span class="info-value">{{ $client->nap_box }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Due Date</span>
                <span class="info-value">{{ $client->due_date_time?->format('M d, Y') }}</span>
            </div>
        </div>

        <!-- Technician -->
        @if($job->technician)
        <p class="section-title">👷 Installed By</p>
        <div class="tech-card">
            <div class="tech-name">{{ $job->technician->name }}</div>
            @if($job->technician->phone_number)
            <div class="tech-detail">📞 {{ $job->technician->phone_number }}</div>
            @endif
            <div class="tech-detail" style="margin-top:4px;">
                🔧 {{ ucfirst(str_replace('_', ' ', $job->job_type)) }} — Completed {{ $job->completed_at?->format('M d, Y h:i A') }}
            </div>
            @if($job->completion_notes)
            <div class="tech-detail" style="margin-top:4px;">📝 {{ $job->completion_notes }}</div>
            @endif
        </div>
        @endif

        <!-- Portal Credentials -->
        @if($tempPassword)
        <p class="section-title">🔑 Client Portal Access</p>
        <div class="creds-card">
            <p class="creds-title">Your Login Credentials</p>
            <div class="creds-row">
                <span class="creds-label">Portal URL</span>
                <span class="creds-value">{{ $portalUrl }}</span>
            </div>
            <div class="creds-row">
                <span class="creds-label">Email</span>
                <span class="creds-value">{{ $client->email }}</span>
            </div>
            <div class="creds-row">
                <span class="creds-label">Password</span>
                <span class="creds-value">{{ $tempPassword }}</span>
            </div>
        </div>

        <div class="cta">
            <a href="{{ $magicLoginUrl }}" class="cta-btn">🚀 Click Here to Login to Your Portal</a>
        </div>
        <p style="text-align:center;font-size:12px;color:#94a3b8;margin-top:8px;">This link logs you in automatically and expires in 7 days.</p>

        <div class="notice">
            <strong>🔐 Manual Login:</strong> You can also log in at <strong>{{ $portalUrl }}</strong><br>
            Email: <strong>{{ $client->email }}</strong> &nbsp;|&nbsp; Password: <strong>{{ $tempPassword }}</strong><br><br>
            Please change your password after logging in from Profile Settings.
        </div>
        @endif

        <div class="divider"></div>

        <p style="font-size:13px;color:#475569;line-height:1.7;text-align:center;">
            Thank you for choosing <strong>{{ config('app.name') }}</strong>.<br>
            Welcome to the family — enjoy your connection! 🌐
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            <strong>{{ config('app.name') }}</strong><br>
            This is an automated email. Please do not reply directly to this message.<br>
            For support, log in to your portal and submit a support ticket.
        </p>
    </div>

</div>
</body>
</html>
