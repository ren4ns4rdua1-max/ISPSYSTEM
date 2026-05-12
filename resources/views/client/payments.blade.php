@extends('client.layout')
@section('title', 'Payments')
@section('page-title', 'Payments')
@section('page-subtitle', 'Payment history and submit proof of payment')

@section('content')
<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start;">

    {{-- Payment History --}}
    <div style="background:white;border-radius:18px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #f8fafc;">
            <h3 class="font-display" style="font-size:15px;font-weight:700;color:#0f172a;">💳 Payment History</h3>
        </div>
        @if($payments->isEmpty())
            <div style="padding:48px;text-align:center;"><p style="font-size:14px;color:#94a3b8;">No payments recorded yet.</p></div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#fafafa;">
                            <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Receipt #</th>
                            <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Date</th>
                            <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Amount</th>
                            <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $pay)
                        <tr style="border-top:1px solid #f8fafc;">
                            <td style="padding:13px 20px;font-size:13px;font-weight:700;color:#0f172a;">{{ $pay->receipt_number }}</td>
                            <td style="padding:13px 20px;font-size:13px;color:#475569;">{{ $pay->payment_date->format('M d, Y') }}</td>
                            <td style="padding:13px 20px;font-size:14px;font-weight:700;color:#059669;">₱{{ number_format($pay->amount,2) }}</td>
                            <td style="padding:13px 20px;">
                                <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#f1f5f9;color:#475569;">{{ $pay->payment_method_label }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
            <div style="padding:14px 20px;border-top:1px solid #f8fafc;">{{ $payments->links() }}</div>
            @endif
        @endif
    </div>

    {{-- Submit Payment Proof --}}
    <div style="background:white;border-radius:18px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);padding:22px;">
        <h3 class="font-display" style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:16px;">📤 Submit Payment Proof</h3>

        @if($unpaid->isEmpty())
            <div style="padding:20px;background:#f0fdf4;border-radius:12px;text-align:center;">
                <p style="font-size:13px;color:#059669;font-weight:600;">✅ No outstanding invoices!</p>
            </div>
        @else
            <form method="POST" action="{{ route('portal.payments.proof') }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Select Invoice</label>
                    <select name="billing_id" required style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;">
                        <option value="">-- Select Invoice --</option>
                        @foreach($unpaid as $bill)
                            <option value="{{ $bill->id }}">{{ $bill->invoice_number }} — ₱{{ number_format($bill->total_amount,2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Payment Method</label>
                    <select name="method" required style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;">
                        <option value="gcash">GCash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="paymaya">PayMaya</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Reference / Transaction #</label>
                    <input type="text" name="reference" placeholder="e.g. GCash ref #123456" style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box;">
                </div>
                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Proof of Payment (Screenshot)</label>
                    <input type="file" name="proof" accept="image/*" required style="width:100%;padding:10px 12px;border:1.5px dashed #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;background:#f9fafb;box-sizing:border-box;">
                </div>
                <button type="submit" style="width:100%;padding:12px;background:linear-gradient(135deg,#059669,#047857);color:white;border:none;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;">
                    Submit Payment Proof
                </button>
                @error('proof')<p style="color:#dc2626;font-size:12px;margin-top:6px;">{{ $message }}</p>@enderror
            </form>
        @endif
    </div>
</div>
@endsection
