<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Approved</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #0f172a; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); padding: 40px 40px 32px; text-align: center; }
        .header-icon { width: 64px; height: 64px; background: rgba(255,255,255,.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .header h1 { color: #ffffff; font-size: 24px; font-weight: 800; letter-spacing: -.3px; }
        .header p { color: rgba(255,255,255,.8); font-size: 14px; margin-top: 6px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        .intro { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 28px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: 12px; }
        .info-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .info-row:last-child { border-bottom: none; padding-bottom: 0; }
        .info-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; flex-shrink: 0; width: 140px; }
        .info-value { font-size: 13px; color: #1e293b; font-weight: 600; text-align: right; }
        .technician-card { background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; border-radius: 14px; padding: 20px; margin-bottom: 20px; }
        .technician-card .tech-name { font-size: 16px; font-weight: 800; color: #1d4ed8; margin-bottom: 4px; }
        .technician-card .tech-detail { font-size: 13px; color: #3b82f6; }
        .schedule-card { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1px solid #bbf7d0; border-radius: 14px; padding: 20px; margin-bottom: 20px; }
        .schedule-card .schedule-date { font-size: 18px; font-weight: 800; color: #15803d; }
        .schedule-card .schedule-label { font-size: 12px; color: #16a34a; margin-top: 2px; }
        .notice { background: #fefce8; border: 1px solid #fde68a; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; font-size: 13px; color: #92400e; line-height: 1.6; }
        .notice strong { color: #78350f; }
        .cta { text-align: center; margin: 28px 0; }
        .cta-btn { display: inline-block; background: linear-gradient(135deg, #dc2626, #b91c1c); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 50px; font-size: 14px; font-weight: 700; letter-spacing: .3px; }
        .divider { height: 1px; background: #f1f5f9; margin: 24px 0; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #94a3b8; line-height: 1.7; }
        .footer strong { color: #64748b; }
        .badge { display: inline-block; background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 50px; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Header -->
    <div class="header">
        <div class="header-icon">
            <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1>Application Approved! 🎉</h1>
        <p>Your internet service application has been reviewed and approved</p>
    </div>

    <!-- Body -->
    <div class="body">

        <p class="greeting">Hello, {{ $client->name }}!</p>
        <p class="intro">
            @if(!empty($customMessage))
                {!! nl2br(e($customMessage)) !!}
            @else
                Great news! Your internet service application has been <strong>approved</strong> by our admin team.
                Your connection will be set up soon. Below are your account details and installation information.
            @endif
        </p>

        <!-- Account Info -->
        <p class="section-title">📋 Your Account Details</p>
        <div class="info-card">
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
                <span class="info-label">PPPoE Username</span>
                <span class="info-value">{{ $client->pppoe_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">PPPoE Password</span>
                <span class="info-value" style="font-family:monospace;background:#f1f5f9;padding:2px 8px;border-radius:6px;">{{ $client->pppoe_password }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Barangay</span>
                <span class="info-value">{{ $client->barangay }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Plan</span>
                <span class="info-value">{{ $client->plan_description }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value"><span class="badge">✓ Active</span></span>
            </div>
        </div>

        @if($technician)
        <!-- Technician Info -->
        <p class="section-title">👷 Your Assigned Technician</p>
        <div class="technician-card">
            <div class="tech-name">{{ $technician->name }}</div>
            @if($technician->phone_number)
            <div class="tech-detail">📞 {{ $technician->phone_number }}</div>
            @endif
            @if($technician->specialization)
            <div class="tech-detail" style="margin-top:4px;">🔧 Specialization: {{ ucfirst($technician->specialization) }}</div>
            @endif
            @if($technician->area_coverage)
            <div class="tech-detail" style="margin-top:4px;">📍 Area: {{ $technician->area_coverage }}</div>
            @endif
        </div>

        @if($job && $job->scheduled_date)
        <!-- Schedule Info -->
        <p class="section-title">📅 Installation Schedule</p>
        <div class="schedule-card">
            <div class="schedule-date">{{ \Carbon\Carbon::parse($job->scheduled_date)->format('l, F j, Y') }}</div>
            <div class="schedule-label">{{ \Carbon\Carbon::parse($job->scheduled_date)->format('g:i A') }} — {{ ucfirst(str_replace('_', ' ', $job->job_type)) }}</div>
        </div>
        @endif

        <div class="notice">
            <strong>📌 What to expect:</strong><br>
            Our technician <strong>{{ $technician->name }}</strong> will visit your home to install and configure your internet connection.
            Please make sure someone is available at your address on the scheduled date.
            @if($technician->phone_number)
            You may contact your technician directly at <strong>{{ $technician->phone_number }}</strong> for any questions.
            @endif
        </div>

        @else
        <div class="notice">
            <strong>📌 Next Steps:</strong><br>
            Our team will contact you shortly to schedule your installation. Please keep your phone available.
            If you have any questions, feel free to reach out to our support team.
        </div>
        @endif

        <div class="divider"></div>

        <p style="font-size:13px;color:#475569;line-height:1.7;text-align:center;">
            Thank you for choosing <strong>{{ config('app.name') }}</strong>.<br>
            We're excited to get you connected!
        </p>

    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            <strong>{{ config('app.name') }}</strong><br>
            This is an automated email. Please do not reply directly to this message.<br>
            If you have questions, contact our support team.
        </p>
    </div>

</div>
</body>
</html>
