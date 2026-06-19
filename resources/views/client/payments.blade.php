@extends('client.layout')
@section('title', 'Payments')
@section('page-title', 'Payments')
@section('page-subtitle', 'Payment history and submit proof of payment')

@section('styles')
@media print {
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    body > * { display: none !important; }
    #receipt-print-area { display: block !important; position: static !important; }
}
#receipt-print-area { display: none; }
@endsection

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
                            <th style="padding:12px 20px;text-align:center;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Action</th>
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
                            <td style="padding:13px 20px;text-align:center;">
                                <button onclick="printReceipt({{ $pay->id }})"
                                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:10px;border:1.5px solid #d1fae5;background:#ecfdf5;color:#059669;font-size:11px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;"
                                    onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='#ecfdf5'">
                                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Receipt
                                </button>
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

{{-- Hidden printable receipt area --}}
<div id="receipt-print-area"></div>

{{-- Receipt data as JSON --}}
<script>
const RECEIPTS = {
    @foreach($payments as $pay)
    {{ $pay->id }}: {
        receipt_number: "{{ $pay->receipt_number }}",
        payment_date:   "{{ $pay->payment_date->format('F d, Y') }}",
        amount:         "{{ number_format($pay->amount, 2) }}",
        method:         "{{ $pay->payment_method_label }}",
        reference:      "{{ addslashes($pay->payment_reference ?? '') }}",
        invoice_no:     "{{ $pay->billing->invoice_number ?? 'N/A' }}",
        plan:           "{{ $pay->billing->subscriptionRate->plan_name ?? 'N/A' }}",
        notes:          "{{ addslashes($pay->notes ?? '') }}",
        client_name:    "{{ addslashes($client->name) }}",
        client_address: "{{ addslashes($client->address ?? '') }}",
        client_phone:   "{{ addslashes($client->phone_number ?? '') }}",
        client_email:   "{{ addslashes($client->email ?? '') }}",
        account_no:     "{{ str_pad($client->id, 6, '0', STR_PAD_LEFT) }}",
    },
    @endforeach
};

const APP_NAME = "{{ config('app.name') }}";

function printReceipt(id) {
    const r = RECEIPTS[id];
    if (!r) return;

    document.getElementById('receipt-print-area').innerHTML = `
    <div style="font-family:'Segoe UI',Arial,sans-serif;max-width:480px;margin:0 auto;padding:40px 44px;color:#1e293b;">

        <!-- Header -->
        <div style="text-align:center;margin-bottom:28px;">
            <div style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#059669,#047857);margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
                <svg style="width:28px;height:28px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 4px;">${APP_NAME}</h1>
            <p style="font-size:12px;color:#64748b;margin:0;">Official Payment Receipt</p>
        </div>

        <!-- Divider -->
        <div style="height:2px;background:linear-gradient(90deg,#059669,#34d399,transparent);margin-bottom:24px;border-radius:2px;"></div>

        <!-- Receipt No + Date -->
        <div style="display:flex;justify-content:space-between;align-items:center;background:#f0fdf4;border-radius:12px;padding:14px 16px;margin-bottom:22px;">
            <div>
                <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#6ee7b7;margin:0 0 2px;">Receipt Number</p>
                <p style="font-size:16px;font-weight:800;color:#064e3b;margin:0;">${r.receipt_number}</p>
            </div>
            <div style="text-align:right;">
                <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#6ee7b7;margin:0 0 2px;">Payment Date</p>
                <p style="font-size:13px;font-weight:700;color:#064e3b;margin:0;">${r.payment_date}</p>
            </div>
        </div>

        <!-- Client Info -->
        <div style="margin-bottom:20px;">
            <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin:0 0 8px;">Received From</p>
            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 2px;">${r.client_name}</p>
            <p style="font-size:12px;color:#64748b;margin:0 0 1px;">Account #: ${r.account_no}</p>
            ${r.client_address ? `<p style="font-size:12px;color:#64748b;margin:0 0 1px;">${r.client_address}</p>` : ''}
            ${r.client_phone   ? `<p style="font-size:12px;color:#64748b;margin:0 0 1px;">${r.client_phone}</p>`   : ''}
        </div>

        <!-- Payment Details -->
        <table style="width:100%;border-collapse:collapse;margin-bottom:20px;font-size:13px;">
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:9px 0;color:#64748b;">Invoice #</td>
                <td style="padding:9px 0;text-align:right;font-weight:600;color:#0f172a;">${r.invoice_no}</td>
            </tr>
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:9px 0;color:#64748b;">Plan</td>
                <td style="padding:9px 0;text-align:right;font-weight:600;color:#0f172a;">${r.plan}</td>
            </tr>
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:9px 0;color:#64748b;">Payment Method</td>
                <td style="padding:9px 0;text-align:right;font-weight:600;color:#0f172a;">${r.method}</td>
            </tr>
            ${r.reference ? `
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:9px 0;color:#64748b;">Reference #</td>
                <td style="padding:9px 0;text-align:right;font-weight:600;color:#0f172a;">${r.reference}</td>
            </tr>` : ''}
        </table>

        <!-- Amount Paid -->
        <div style="background:linear-gradient(135deg,#059669,#047857);border-radius:14px;padding:18px 20px;text-align:center;margin-bottom:24px;">
            <p style="color:rgba(255,255,255,.75);font-size:12px;font-weight:600;margin:0 0 4px;text-transform:uppercase;letter-spacing:.06em;">Amount Paid</p>
            <p style="color:white;font-size:32px;font-weight:800;margin:0;">₱${r.amount}</p>
        </div>

        ${r.notes ? `<div style="background:#f8fafc;border-radius:10px;padding:12px 14px;margin-bottom:20px;"><p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin:0 0 4px;">Notes</p><p style="font-size:12px;color:#475569;margin:0;">${r.notes}</p></div>` : ''}

        <!-- Footer -->
        <div style="border-top:1px solid #f1f5f9;padding-top:18px;text-align:center;">
            <div style="display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;border-radius:20px;padding:6px 16px;margin-bottom:10px;">
                <svg style="width:14px;height:14px;color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span style="font-size:12px;font-weight:700;color:#059669;">Payment Confirmed</span>
            </div>
            <p style="font-size:11px;color:#94a3b8;margin:0 0 3px;">Thank you for your payment, ${r.client_name}!</p>
            <p style="font-size:10px;color:#cbd5e1;margin:0;">This is an official receipt from ${APP_NAME}.</p>
        </div>
    </div>`;

    window.print();
}
</script>
@endsection
