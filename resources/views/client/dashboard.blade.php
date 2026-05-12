@extends('client.layout')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . $client->name)

@section('content')
@php
    $job = $client->installationJobs->first();
    $lastBilling = $client->billings->first();
@endphp

{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
    {{-- Account Status --}}
    <div style="background:white;border-radius:18px;padding:20px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);">
        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:8px;">Account Status</p>
        <span class="status-{{ $client->status }}" style="font-size:13px;font-weight:700;padding:5px 14px;border-radius:20px;display:inline-block;">{{ ucfirst(str_replace('_',' ',$client->status)) }}</span>
    </div>
    {{-- Current Plan --}}
    <div style="background:white;border-radius:18px;padding:20px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);">
        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:6px;">Current Plan</p>
        <p style="font-size:15px;font-weight:700;color:#0f172a;">{{ $client->plan_description ?: 'N/A' }}</p>
        @if($client->subscriptionRate)
            <p style="font-size:12px;color:#64748b;margin-top:2px;">{{ $client->subscriptionRate->speed }} · ₱{{ number_format($client->subscriptionRate->monthly_fee,0) }}/mo</p>
        @endif
    </div>
    {{-- Balance Due --}}
    <div style="background:{{ $balance > 0 ? '#fef2f2' : '#f0fdf4' }};border-radius:18px;padding:20px;border:1px solid {{ $balance > 0 ? '#fecaca' : '#bbf7d0' }};box-shadow:0 2px 8px rgba(0,0,0,.03);">
        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:6px;">Balance Due</p>
        <p style="font-size:22px;font-weight:800;color:{{ $balance > 0 ? '#dc2626' : '#059669' }};">₱{{ number_format($balance,2) }}</p>
        @if($overdue > 0)<p style="font-size:11px;color:#dc2626;font-weight:600;margin-top:2px;">{{ $overdue }} overdue invoice(s)</p>@endif
    </div>
    {{-- Due Date --}}
    <div style="background:white;border-radius:18px;padding:20px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);">
        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:6px;">Next Due Date</p>
        <p style="font-size:15px;font-weight:700;color:#0f172a;">{{ $client->due_date_time ? $client->due_date_time->format('M d, Y') : 'N/A' }}</p>
        @if($client->due_date_time && $client->due_date_time->isPast())
            <p style="font-size:11px;color:#dc2626;font-weight:600;margin-top:2px;">⚠ Overdue</p>
        @elseif($client->due_date_time)
            <p style="font-size:11px;color:#64748b;margin-top:2px;">{{ $client->due_date_time->diffForHumans() }}</p>
        @endif
    </div>
    {{-- Last Payment --}}
    <div style="background:white;border-radius:18px;padding:20px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);">
        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:6px;">Last Payment</p>
        @if($lastPay)
            <p style="font-size:15px;font-weight:700;color:#0f172a;">₱{{ number_format($lastPay->amount,2) }}</p>
            <p style="font-size:11px;color:#64748b;margin-top:2px;">{{ $lastPay->payment_date->format('M d, Y') }} · {{ $lastPay->payment_method_label }}</p>
        @else
            <p style="font-size:13px;color:#94a3b8;">No payments yet</p>
        @endif
    </div>
    {{-- Account Number --}}
    <div style="background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:18px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.08);">
        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.4);margin-bottom:6px;">Account Number</p>
        <p style="font-size:18px;font-weight:800;color:white;letter-spacing:.05em;">#{{ str_pad($client->id,6,'0',STR_PAD_LEFT) }}</p>
        <p style="font-size:11px;color:rgba(255,255,255,.4);margin-top:2px;">PPPoE: {{ $client->pppoe_name }}</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    {{-- Subscription Details --}}
    <div style="background:white;border-radius:18px;padding:22px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);">
        <h3 class="font-display" style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:16px;">📡 My Subscription</h3>
        @php $rate = $client->subscriptionRate; @endphp
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach([
                ['Plan', $client->plan_description ?: 'N/A'],
                ['Speed', $rate?->speed ?: 'N/A'],
                ['Monthly Fee', $rate ? '₱'.number_format($rate->monthly_fee,2) : 'N/A'],
                ['Activation Date', $client->start_date?->format('M d, Y') ?: 'N/A'],
                ['Barangay', $client->barangay],
                ['NAP Box', $client->nap_box],
            ] as [$label, $value])
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f8fafc;">
                <span style="font-size:12px;color:#94a3b8;font-weight:600;">{{ $label }}</span>
                <span style="font-size:13px;color:#0f172a;font-weight:600;">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Service Status --}}
    <div style="background:white;border-radius:18px;padding:22px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);">
        <h3 class="font-display" style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:16px;">🔧 Service Status</h3>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f8fafc;">
                <span style="font-size:12px;color:#94a3b8;font-weight:600;">Connection</span>
                <span style="font-size:13px;font-weight:700;padding:3px 12px;border-radius:20px;{{ $client->status === 'active' ? 'background:#ecfdf5;color:#059669;' : 'background:#fef2f2;color:#dc2626;' }}">
                    {{ $client->status === 'active' ? '🟢 Online' : '🔴 Offline' }}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f8fafc;">
                <span style="font-size:12px;color:#94a3b8;font-weight:600;">Service Type</span>
                <span style="font-size:13px;color:#0f172a;font-weight:600;">{{ $rate?->plan_type ?? 'Fiber' }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f8fafc;">
                <span style="font-size:12px;color:#94a3b8;font-weight:600;">Installation Date</span>
                <span style="font-size:13px;color:#0f172a;font-weight:600;">{{ $client->start_date?->format('M d, Y') ?: 'N/A' }}</span>
            </div>
            @if($job && $job->technician)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f8fafc;">
                <span style="font-size:12px;color:#94a3b8;font-weight:600;">Assigned Technician</span>
                <span style="font-size:13px;color:#0f172a;font-weight:600;">{{ $job->technician->name }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;">
                <span style="font-size:12px;color:#94a3b8;font-weight:600;">Job Status</span>
                <span style="font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px;background:#eff6ff;color:#1d4ed8;">{{ ucfirst(str_replace('_',' ',$job->status)) }}</span>
            </div>
            @else
            <div style="padding:16px;background:#f8fafc;border-radius:12px;text-align:center;">
                <p style="font-size:12px;color:#94a3b8;">No active installation job</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap;">
    <a href="{{ route('portal.billing') }}" style="display:inline-flex;align-items:center;gap:8px;padding:12px 20px;background:linear-gradient(135deg,#dc2626,#b91c1c);color:white;border-radius:14px;font-size:13px;font-weight:700;text-decoration:none;">
        📄 View Invoices
    </a>
    <a href="{{ route('portal.payments') }}" style="display:inline-flex;align-items:center;gap:8px;padding:12px 20px;background:linear-gradient(135deg,#059669,#047857);color:white;border-radius:14px;font-size:13px;font-weight:700;text-decoration:none;">
        💳 Make Payment
    </a>
    <a href="{{ route('portal.tickets') }}" style="display:inline-flex;align-items:center;gap:8px;padding:12px 20px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border-radius:14px;font-size:13px;font-weight:700;text-decoration:none;">
        🎫 Support Ticket
    </a>
</div>
@endsection
