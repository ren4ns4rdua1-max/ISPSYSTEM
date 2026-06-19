<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Approved</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #0f172a; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); padding: 40px 40px 32px; text-align: center; }
        .header-icon { width: 64px; height: 64px; background: rgba(255,255,255,.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .header h1 { color: #ffffff; font-size: 24px; font-weight: 800; }
        .header p { color: rgba(255,255,255,.85); font-size: 14px; margin-top: 6px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        .intro { font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 28px; }
        .amount-highlight { font-size: 28px; font-weight: 800; color: #16a34a; text-align: center; padding: 20px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; margin-bottom: 20px; }
        .amount-highlight span { font-size: 13px; color: #15803d; display: block; margin-top: 4px; font-weight: 600; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: 12px; }
        .info-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .info-row:last-child { border-bottom: none; padding-bottom: 0; }
        .info-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; width: 160px; flex-shrink: 0; }
        .info-value { font-size: 13px; color: #1e293b; font-weight: 600; text-align: right; }
        .success-notice { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; font-size: 13px; color: #15803d; line-height: 1.6; }
        .divider { height: 1px; background: #f1f5f9; margin: 24px 0; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #94a3b8; line-height: 1.7; }
        .footer strong { color: #64748b; }
        .badge-paid { display: inline-block; background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 50px; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="header-icon">
            <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1>Payment Approved ✅</h1>
        <p>Your payment has been verified and confirmed</p>
    </div>

    <div class="body">

        <p class="greeting">Hello, {{ $payment->client->name }}!</p>
        <p class="intro">
            Great news! Your payment for invoice <strong>{{ $payment->billing?->invoice_number ?? 'N/A' }}</strong> has been
            <strong>reviewed and approved</strong> by our admin team. Your account is now up to date.
        </p>

        <div class="amount-highlight">
            ₱{{ number_format($payment->amount, 2) }}
            <span>Payment Confirmed</span>
        </div>

        <p class="section-title">📋 Payment Details</p>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Receipt #</span>
                <span class="info-value">{{ $payment->receipt_number }}</span>
            </div>
            @if($payment->billing)
            <div class="info-row">
                <span class="info-label">Invoice #</span>
                <span class="info-value">{{ $payment->billing->invoice_number }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Payment Method</span>
                <span class="info-value">{{ $payment->payment_method_label }}</span>
            </div>
            @if($payment->payment_reference)
            <div class="info-row">
                <span class="info-label">Reference</span>
                <span class="info-value">{{ $payment->payment_reference }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Payment Date</span>
                <span class="info-value">{{ $payment->payment_date->format('F j, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Approved On</span>
                <span class="info-value">{{ $payment->approved_at?->format('F j, Y g:i A') ?? now()->format('F j, Y g:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value"><span class="badge-paid">✓ Approved</span></span>
            </div>
        </div>

        <div class="success-notice">
            <strong>✅ Your billing status has been updated.</strong><br>
            This payment has been applied to your account. If you have any questions about this transaction,
            please contact our support team.
        </div>

        <div class="divider"></div>

        <p style="font-size:13px;color:#475569;line-height:1.7;text-align:center;">
            Thank you for your payment and for being a valued customer of <strong>{{ config('app.name') }}</strong>.
        </p>

    </div>

    <div class="footer">
        <p>
            <strong>{{ config('app.name') }}</strong><br>
            This is an automated confirmation. Please do not reply directly to this message.
        </p>
    </div>

</div>
</body>
</html>
