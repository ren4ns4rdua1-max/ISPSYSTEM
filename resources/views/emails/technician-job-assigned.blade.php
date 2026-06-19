<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Job Assigned</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #0f172a; }
        .wrapper { max-width: 580px; margin: 32px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); padding: 36px 40px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 800; margin-top: 12px; }
        .header p { color: rgba(255,255,255,.8); font-size: 13px; margin-top: 6px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 17px; font-weight: 700; margin-bottom: 10px; }
        .text { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 20px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: 10px; }
        .card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; margin-bottom: 18px; }
        .row { display: flex; justify-content: space-between; align-items: flex-start; padding: 7px 0; border-bottom: 1px solid #f1f5f9; }
        .row:last-child { border-bottom: none; padding-bottom: 0; }
        .label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .value { font-size: 13px; color: #1e293b; font-weight: 600; text-align: right; }
        .badge { display: inline-block; background: #fef3c7; color: #d97706; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 50px; border: 1px solid #fde68a; }
        .schedule-card { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1px solid #bbf7d0; border-radius: 14px; padding: 18px; margin-bottom: 18px; }
        .schedule-date { font-size: 18px; font-weight: 800; color: #15803d; }
        .schedule-label { font-size: 12px; color: #16a34a; margin-top: 3px; }
        .notice { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 14px 18px; font-size: 13px; color: #1d4ed8; line-height: 1.6; margin-bottom: 20px; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; line-height: 1.7; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <svg width="40" height="40" fill="none" stroke="white" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <h1>New Job Assigned! 🔧</h1>
        <p>You have a new installation job waiting for you</p>
    </div>
    <div class="body">
        <p class="greeting">Hello, {{ $technician->name }}!</p>
        <p class="text">
            A new job has been assigned to you on <strong>{{ config('app.name') }}</strong>.
            Please review the details below and be ready on the scheduled date.
        </p>

        <p class="section-title">📋 Job Details</p>
        <div class="card">
            <div class="row">
                <span class="label">Job Type</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $job->job_type)) }}</span>
            </div>
            <div class="row">
                <span class="label">Status</span>
                <span class="value"><span class="badge">{{ ucfirst($job->status) }}</span></span>
            </div>
            @if($job->notes)
            <div class="row">
                <span class="label">Notes</span>
                <span class="value" style="max-width:60%;text-align:right;">{{ $job->notes }}</span>
            </div>
            @endif
        </div>

        <p class="section-title">👤 Client Information</p>
        <div class="card">
            <div class="row">
                <span class="label">Name</span>
                <span class="value">{{ $job->client->name }}</span>
            </div>
            <div class="row">
                <span class="label">Phone</span>
                <span class="value">{{ $job->client->phone_number }}</span>
            </div>
            <div class="row">
                <span class="label">Address</span>
                <span class="value">{{ $job->client->barangay }}</span>
            </div>
            <div class="row">
                <span class="label">Plan</span>
                <span class="value">{{ $job->client->plan_description }}</span>
            </div>
        </div>

        @if($job->scheduled_date)
        <p class="section-title">📅 Schedule</p>
        <div class="schedule-card">
            <div class="schedule-date">{{ \Carbon\Carbon::parse($job->scheduled_date)->format('l, F j, Y') }}</div>
            <div class="schedule-label">{{ \Carbon\Carbon::parse($job->scheduled_date)->format('g:i A') }}</div>
        </div>
        @endif

        <div class="notice">
            <strong>📌 Reminder:</strong> Please contact the client before arriving.
            Log in to your technician dashboard to update the job status once you start and complete the work.
        </div>
    </div>
    <div class="footer">
        <strong>{{ config('app.name') }}</strong><br>
        This is an automated notification. Please do not reply directly to this email.
    </div>
</div>
</body>
</html>
