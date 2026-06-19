@extends('client.layout')
@section('title', 'Billing & Invoices')
@section('page-title', 'Billing & Invoices')
@section('page-subtitle', 'View all your invoices and payment status')

@section('styles')
@media print {
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    body > * { display: none !important; }
    #invoice-print-area { display: block !important; position: static !important; }
}
#invoice-print-area { display: none; }
@endsection

@section('content')

{{-- Invoice list --}}
<div style="background:white;border-radius:18px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);overflow:hidden;">
    <div style="padding:20px 24px;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between;">
        <h3 class="font-display" style="font-size:15px;font-weight:700;color:#0f172a;">📄 All Invoices</h3>
        <span style="font-size:12px;color:#64748b;font-weight:600;">{{ $billings->total() }} total</span>
    </div>

    @if($billings->isEmpty())
        <div style="padding:48px;text-align:center;">
            <p style="font-size:14px;color:#94a3b8;">No invoices found.</p>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#fafafa;">
                        <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Invoice #</th>
                        <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Billing Date</th>
                        <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Due Date</th>
                        <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Amount</th>
                        <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Status</th>
                        <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Type</th>
                        <th style="padding:12px 20px;text-align:center;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($billings as $bill)
                    <tr style="border-top:1px solid #f8fafc;transition:background .15s;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                        <td style="padding:14px 20px;">
                            <span style="font-size:13px;font-weight:700;color:#0f172a;">{{ $bill->invoice_number }}</span>
                        </td>
                        <td style="padding:14px 20px;font-size:13px;color:#475569;">{{ $bill->billing_date->format('M d, Y') }}</td>
                        <td style="padding:14px 20px;font-size:13px;color:{{ $bill->due_date->isPast() && $bill->status !== 'paid' ? '#dc2626' : '#475569' }};font-weight:{{ $bill->due_date->isPast() && $bill->status !== 'paid' ? '700' : '400' }};">
                            {{ $bill->due_date->format('M d, Y') }}
                        </td>
                        <td style="padding:14px 20px;font-size:14px;font-weight:700;color:#0f172a;">₱{{ number_format($bill->total_amount,2) }}</td>
                        <td style="padding:14px 20px;">
                            @php $c = $bill->status_color; @endphp
                            @php $hasPendingPayment = $bill->payments()->where('approval_status','pending')->exists(); @endphp
                            @if($hasPendingPayment)
                                <span style="font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;display:inline-block;background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;">⏳ Pending Approval</span>
                            @else
                                <span class="{{ $c['bg'] }} {{ $c['text'] }}" style="font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;display:inline-block;">{{ $c['label'] }}</span>
                            @endif
                        </td>
                        <td style="padding:14px 20px;font-size:12px;color:#64748b;font-weight:600;">{{ ucfirst($bill->billing_type) }}</td>
                        <td style="padding:14px 20px;text-align:center;">
                            <div style="display:inline-flex;align-items:center;gap:6px;">
                                <button onclick="printInvoice({{ $bill->id }})"
                                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:10px;border:1.5px solid #e0e7ff;background:#eef2ff;color:#4338ca;font-size:11px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;"
                                    onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
                                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Print
                                </button>
                                @if(!in_array($bill->status, ['paid','cancelled']))
                                <button onclick="openPayModal({{ $bill->id }}, '{{ $bill->invoice_number }}', '{{ number_format($bill->total_amount,2) }}')"
                                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:10px;border:1.5px solid #bbf7d0;background:#dcfce7;color:#15803d;font-size:11px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;"
                                    onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#dcfce7'">
                                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    Pay Now
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($billings->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f8fafc;">
            {{ $billings->links() }}
        </div>
        @endif
    @endif
</div>

{{-- Hidden printable invoice area --}}
<div id="invoice-print-area">
    {{-- Populated dynamically by JS --}}
</div>

{{-- Invoice data embedded as JSON for JS --}}
<script>
const INVOICES = {
    @foreach($billings as $bill)
    {{ $bill->id }}: {
        invoice_number: "{{ $bill->invoice_number }}",
        billing_date:   "{{ $bill->billing_date->format('F d, Y') }}",
        due_date:       "{{ $bill->due_date->format('F d, Y') }}",
        billing_type:   "{{ ucfirst($bill->billing_type) }}",
        status:         "{{ $bill->status_color['label'] }}",
        status_color:   "{{ $bill->status === 'paid' ? '#059669' : ($bill->status === 'overdue' ? '#dc2626' : '#d97706') }}",
        amount:         "{{ number_format($bill->amount, 2) }}",
        tax_amount:     "{{ number_format($bill->tax_amount, 2) }}",
        discount:       "{{ number_format($bill->discount_amount, 2) }}",
        total:          "{{ number_format($bill->total_amount, 2) }}",
        plan:           "{{ $bill->subscriptionRate->plan_name ?? 'N/A' }}",
        notes:          "{{ addslashes($bill->notes ?? '') }}",
        paid_date:      "{{ $bill->paid_date ? $bill->paid_date->format('F d, Y') : '' }}",
        client_name:    "{{ addslashes($client->name) }}",
        client_address: "{{ addslashes($client->address ?? '') }}",
        client_phone:   "{{ addslashes($client->phone_number ?? '') }}",
        client_email:   "{{ addslashes($client->email ?? '') }}",
        account_no:     "{{ str_pad($client->id, 6, '0', STR_PAD_LEFT) }}",
    },
    @endforeach
};

const APP_NAME = "{{ config('app.name') }}";

function printInvoice(id) {
    const inv = INVOICES[id];
    if (!inv) return;

    const statusBg = inv.status === 'Paid' ? '#dcfce7' : (inv.status === 'Overdue' ? '#fee2e2' : '#fef3c7');

    document.getElementById('invoice-print-area').innerHTML = `
    <div style="font-family:'Segoe UI',Arial,sans-serif;max-width:680px;margin:0 auto;padding:40px 48px;color:#1e293b;">

        <!-- Header -->
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:32px;">
            <div>
                <h1 style="font-size:26px;font-weight:800;color:#0f172a;margin:0 0 4px;">${APP_NAME}</h1>
                <p style="font-size:12px;color:#64748b;margin:0;">Internet Service Provider</p>
            </div>
            <div style="text-align:right;">
                <div style="font-size:22px;font-weight:800;color:#dc2626;margin-bottom:4px;">INVOICE</div>
                <div style="font-size:14px;font-weight:700;color:#0f172a;">${inv.invoice_number}</div>
                <div style="display:inline-block;margin-top:6px;padding:4px 14px;border-radius:20px;font-size:11px;font-weight:700;background:${statusBg};color:${inv.status_color};">${inv.status}</div>
            </div>
        </div>

        <!-- Divider -->
        <div style="height:2px;background:linear-gradient(90deg,#dc2626,#f87171,transparent);margin-bottom:28px;border-radius:2px;"></div>

        <!-- Bill To + Details -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px;margin-bottom:28px;">
            <div>
                <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin:0 0 8px;">Bill To</p>
                <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 3px;">${inv.client_name}</p>
                <p style="font-size:12px;color:#64748b;margin:0 0 2px;">Account #: ${inv.account_no}</p>
                ${inv.client_address ? `<p style="font-size:12px;color:#64748b;margin:0 0 2px;">${inv.client_address}</p>` : ''}
                ${inv.client_phone   ? `<p style="font-size:12px;color:#64748b;margin:0 0 2px;">${inv.client_phone}</p>` : ''}
                ${inv.client_email   ? `<p style="font-size:12px;color:#64748b;margin:0;">${inv.client_email}</p>` : ''}
            </div>
            <div>
                <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin:0 0 8px;">Invoice Details</p>
                <table style="font-size:12px;width:100%;border-collapse:collapse;">
                    <tr><td style="padding:2px 0;color:#64748b;width:110px;">Billing Date</td><td style="font-weight:600;color:#0f172a;">${inv.billing_date}</td></tr>
                    <tr><td style="padding:2px 0;color:#64748b;">Due Date</td><td style="font-weight:600;color:#0f172a;">${inv.due_date}</td></tr>
                    <tr><td style="padding:2px 0;color:#64748b;">Type</td><td style="font-weight:600;color:#0f172a;">${inv.billing_type}</td></tr>
                    <tr><td style="padding:2px 0;color:#64748b;">Plan</td><td style="font-weight:600;color:#0f172a;">${inv.plan}</td></tr>
                    ${inv.paid_date ? `<tr><td style="padding:2px 0;color:#64748b;">Paid On</td><td style="font-weight:600;color:#059669;">${inv.paid_date}</td></tr>` : ''}
                </table>
            </div>
        </div>

        <!-- Line Items -->
        <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#64748b;">Description</th>
                    <th style="padding:10px 14px;text-align:right;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#64748b;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:12px 14px;font-size:13px;color:#0f172a;">Internet Service — ${inv.plan}</td>
                    <td style="padding:12px 14px;text-align:right;font-size:13px;font-weight:600;color:#0f172a;">₱${inv.amount}</td>
                </tr>
                ${parseFloat(inv.tax_amount.replace(/,/g,'')) > 0 ? `
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:12px 14px;font-size:13px;color:#64748b;">Tax</td>
                    <td style="padding:12px 14px;text-align:right;font-size:13px;color:#64748b;">₱${inv.tax_amount}</td>
                </tr>` : ''}
                ${parseFloat(inv.discount.replace(/,/g,'')) > 0 ? `
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:12px 14px;font-size:13px;color:#64748b;">Discount</td>
                    <td style="padding:12px 14px;text-align:right;font-size:13px;color:#059669;">−₱${inv.discount}</td>
                </tr>` : ''}
            </tbody>
        </table>

        <!-- Total -->
        <div style="background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:14px;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;">
            <span style="color:rgba(255,255,255,.7);font-size:13px;font-weight:600;">Total Amount Due</span>
            <span style="color:white;font-size:24px;font-weight:800;">₱${inv.total}</span>
        </div>

        ${inv.notes ? `<div style="background:#f8fafc;border-radius:12px;padding:14px 16px;margin-bottom:24px;"><p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin:0 0 4px;">Notes</p><p style="font-size:12px;color:#475569;margin:0;">${inv.notes}</p></div>` : ''}

        <!-- Footer -->
        <div style="border-top:1px solid #f1f5f9;padding-top:20px;text-align:center;">
            <p style="font-size:11px;color:#94a3b8;margin:0 0 4px;">Thank you for choosing ${APP_NAME}!</p>
            <p style="font-size:10px;color:#cbd5e1;margin:0;">This is a computer-generated invoice. For questions, please contact our support team.</p>
        </div>
    </div>`;

    window.print();
}
</script>

{{-- Pay Now Modal --}}
<div id="pay-modal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.55);backdrop-filter:blur(4px);align-items:center;justify-content:center;" onclick="if(event.target===this)closePayModal()">
    <div style="background:white;border-radius:20px;box-shadow:0 24px 80px rgba(0,0,0,.18);width:100%;max-width:440px;margin:1rem;animation:payModalPop .25s cubic-bezier(0.2,0.9,0.4,1.1) both;">
        <div style="background:linear-gradient(135deg,#15803d,#16a34a);border-radius:20px 20px 0 0;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <p style="color:white;font-weight:800;font-size:16px;font-family:'Syne',sans-serif;margin:0;">&#128179; Pay Invoice</p>
                <p id="pay-modal-invoice" style="color:rgba(255,255,255,.75);font-size:12px;margin:3px 0 0;"></p>
            </div>
            <button onclick="closePayModal()" style="background:rgba(255,255,255,.2);border:none;border-radius:8px;width:30px;height:30px;cursor:pointer;color:white;font-size:18px;display:flex;align-items:center;justify-content:center;">&times;</button>
        </div>
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;margin:20px 24px 0;border-radius:12px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:12px;font-weight:600;color:#15803d;">Amount Due</span>
            <span id="pay-modal-amount" style="font-size:22px;font-weight:800;color:#15803d;"></span>
        </div>
        <form id="pay-now-form" method="POST" action="{{ route('portal.payments.proof') }}" enctype="multipart/form-data" style="padding:20px 24px;">
            @csrf
            <input type="hidden" name="billing_id" id="pay-billing-id">
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#64748b;margin-bottom:6px;">Payment Method</label>
                <select name="method" required style="width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:13px;font-family:'DM Sans',sans-serif;background:#f8fafc;outline:none;">
                    <option value="gcash">GCash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="paymaya">PayMaya</option>
                    <option value="cash">Cash</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#64748b;margin-bottom:6px;">Reference / Transaction No. <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <input type="text" name="reference" placeholder="e.g. GCash ref #123456" style="width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:13px;font-family:'DM Sans',sans-serif;background:#f8fafc;outline:none;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#64748b;margin-bottom:6px;">Payment Proof / Screenshot <span style="color:#dc2626;">*</span></label>
                <input type="file" name="proof" accept="image/*" required style="width:100%;padding:10px 14px;border-radius:10px;border:1.5px dashed #d1fae5;font-size:13px;font-family:'DM Sans',sans-serif;background:#f0fdf4;outline:none;box-sizing:border-box;cursor:pointer;">
                <p style="font-size:11px;color:#94a3b8;margin:5px 0 0;">Upload screenshot of your payment (max 5MB)</p>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="closePayModal()" style="flex:1;padding:11px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;font-family:'DM Sans',sans-serif;">Cancel</button>
                <button type="submit" style="flex:2;padding:11px;border-radius:10px;border:none;background:linear-gradient(135deg,#15803d,#16a34a);font-size:13px;font-weight:700;color:white;cursor:pointer;font-family:'DM Sans',sans-serif;box-shadow:0 4px 14px rgba(21,128,61,.3);">
                    Submit Payment
                </button>
            </div>
        </form>
    </div>
</div>
<style>@keyframes payModalPop{from{opacity:0;transform:scale(.95) translateY(-12px)}to{opacity:1;transform:scale(1) translateY(0)}}</style>
<script>
function openPayModal(billingId, invoiceNo, amount) {
    document.getElementById('pay-billing-id').value = billingId;
    document.getElementById('pay-modal-invoice').textContent = 'Invoice: ' + invoiceNo;
    document.getElementById('pay-modal-amount').textContent = '\u20b1' + amount;
    document.getElementById('pay-modal').style.display = 'flex';
}
function closePayModal() {
    document.getElementById('pay-modal').style.display = 'none';
}
</script>

@endsection
