@extends('client.layout')
@section('title', 'Billing & Invoices')
@section('page-title', 'Billing & Invoices')
@section('page-subtitle', 'View all your invoices and payment status')

@section('content')
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
                            <span class="{{ $c['bg'] }} {{ $c['text'] }}" style="font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;display:inline-block;">{{ $c['label'] }}</span>
                        </td>
                        <td style="padding:14px 20px;font-size:12px;color:#64748b;font-weight:600;">{{ ucfirst($bill->billing_type) }}</td>
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
@endsection
