<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Due Reminder</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #0f172a; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); padding: 40px 40px 32px; text-align: center; }
        .header-icon { width: 64px; height: 64px; background: rgba(255,255,255,.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .header h1 { color: #ffffff; font-size: 24px; font-weight: 800; letter-spacing: -.3px; }
        .header p { color: rgba(255,255,255,.85); font-size: 14px; margin-top: 6px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        .intro { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 28px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: 12px; }
        .info-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .info-row:last-child { border-bottom: none; padding-bottom: 0; }
        .info-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; flex-shrink: 0; width: 160px; }
        .info-value { font-size: 13px; color: #1e293b; font-weight: 600; text-align: right; }
        .amount-highlight { font-size: 28px; font-weight: 800; color: #d97706; text-align: center; padding: 20px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 14px; margin-bottom: 20px; }
        .amount-highlight span { font-size: 13px; color: #92400e; display: block; margin-top: 4px; font-weight: 600; }
        .warning { background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; font-size: 13px; color: #991b1b; line-height: 1.6; }
        .warning strong { color: #7f1d1d; }
        .divider { height: 1px; background: #f1f5f9; margin: 24px 0; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #94a3b8; line-height: 1.7; }
        .footer strong { color: #64748b; }
        .overdue-badge { display: inline-block; background: #fef2f2; color: #dc2626; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 50px; border: 1px solid #fecaca; }
        .pending-badge { display: inline-block; background: #fef3c7; color: #d97706; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 50px; border: 1px solid #fde68a; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="header-icon">
            <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1>Payment Due Reminder ⚠️</h1>
        <p>Action required — please settle your outstanding balance</p>
    </div>

    <div class="body">

        <p class="greeting">Hello, {{ $billing->client->name }}!</p>
        <p class="intro">
            This is a friendly reminder that your invoice <strong>{{ $billing->invoice_number }}</strong> is
            @if($billing->isOverdue())
                <strong>overdue</strong>. Please settle your balance immediately to avoid service suspension.
            @else
                due on <strong>{{ $billing->due_date->format('F j, Y') }}</strong>. Please make sure to pay before the due date to avoid any interruption of service.
            @endif
        </p>

        <div class="amount-highlight">
            ₱{{ number_format($billing->total_amount, 2) }}
            <span>Total Amount Due</span>
        </div>

        <p class="section-title">📋 Invoice Details</p>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Invoice #</span>
                <span class="info-value">{{ $billing->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Billing Type</span>
                <span class="info-value">{{ ucfirst($billing->billing_type) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Billing Date</span>
                <span class="info-value">{{ $billing->billing_date->format('F j, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Due Date</span>
                <span class="info-value" style="color:#d97706;">{{ $billing->due_date->format('F j, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    @if($billing->isOverdue() || $billing->status === 'overdue')
                        <span class="overdue-badge">⚠ Overdue</span>
                    @else
                        <span class="pending-badge">⏳ Pending</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="warning">
            <strong>⚠️ Important Notice:</strong><br>
            Failure to pay by the due date may result in <strong>service suspension</strong>.
            Please contact our support team if you're experiencing any issues with your payment.
        </div>

        <div class="divider"></div>

        <p style="font-size:13px;color:#475569;line-height:1.7;text-align:center;">
            Thank you for being a valued customer of <strong>{{ config('app.name') }}</strong>.<br>
            If you have already made your payment, please disregard this notice.
        </p>

    </div>

    <div class="footer">
        <p>
            <strong>{{ config('app.name') }}</strong><br>
            This is an automated reminder. Please do not reply directly to this message.<br>
            For payment inquiries, contact our billing support team.
        </p>
    </div>

</div>
</body>
</html>
