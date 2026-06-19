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

{{-- Pin My Location Card --}}
<div style="margin-top:20px;background:white;border-radius:18px;padding:22px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#2563eb,#1d4ed8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:20px;height:20px;color:white;" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p style="font-size:15px;font-weight:700;color:#0f172a;">📍 My Location Pin</p>
                @if($client->latitude && $client->longitude)
                    <p style="font-size:11px;color:#059669;font-weight:600;">✓ Location saved — technician can navigate to you</p>
                @else
                    <p style="font-size:11px;color:#f59e0b;font-weight:600;">⚠ No location pinned yet — helps technician find you faster</p>
                @endif
            </div>
        </div>
        <button onclick="openPinModal()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border:none;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;">
            {{ $client->latitude ? '📌 Update Pin' : '📌 Pin My Location' }}
        </button>
    </div>

    {{-- Mini map preview if coords exist --}}
    @if($client->latitude && $client->longitude)
    <div id="mini-map" style="height:180px;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;"></div>
    @endif
</div>

{{-- Location Pin Modal --}}
<div id="pin-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div style="background:white;border-radius:24px;width:92%;max-width:680px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,.4);overflow:hidden;">
        <div style="padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div>
                <p style="font-weight:700;font-size:15px;color:#0f172a;">📌 Pin Your Location</p>
                <p style="font-size:12px;color:#64748b;margin-top:2px;">Drag the pin or click on the map to set your exact location</p>
            </div>
            <button onclick="closePinModal()" style="width:32px;height:32px;border-radius:10px;background:#f1f5f9;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div style="padding:16px;flex:1;overflow:hidden;">
            <div id="pin-map" style="height:340px;border-radius:14px;"></div>
        </div>

        <div style="padding:0 16px 16px;flex-shrink:0;">
            <div id="coords-display" style="text-align:center;font-size:12px;color:#64748b;font-weight:600;margin-bottom:12px;min-height:18px;"></div>
            <div style="display:flex;gap:10px;">
                <button onclick="closePinModal()" style="flex:1;padding:12px;background:#f1f5f9;border:none;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;color:#475569;">Cancel</button>
                <button id="save-pin-btn" onclick="savePin()" style="flex:1;padding:12px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border:none;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;">💾 Save Pin</button>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet + Pin JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var pinMap = null, pinMarker = null;
    var savedLat = {{ $client->latitude ?? 'null' }};
    var savedLng = {{ $client->longitude ?? 'null' }};

    @if($client->latitude && $client->longitude)
    document.addEventListener('DOMContentLoaded', function() {
        var mm = L.map('mini-map', { zoomControl:false, dragging:false, scrollWheelZoom:false, attributionControl:false })
                  .setView([{{ $client->latitude }}, {{ $client->longitude }}], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mm);
        var icon = L.divIcon({
            html: '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="38" viewBox="0 0 32 42"><path d="M16 0C7.163 0 0 7.163 0 16c0 10.667 16 26 16 26S32 26.667 32 16C32 7.163 24.837 0 16 0z" fill="#2563eb"/><circle cx="16" cy="16" r="7" fill="white"/><circle cx="16" cy="16" r="4" fill="#2563eb"/></svg>',
            className:'', iconSize:[28,38], iconAnchor:[14,38]
        });
        L.marker([{{ $client->latitude }}, {{ $client->longitude }}], {icon:icon}).addTo(mm);
    });
    @endif

    function openPinModal() {
        document.getElementById('pin-modal').style.display = 'flex';
        setTimeout(initPinMap, 80);
    }

    function closePinModal() {
        document.getElementById('pin-modal').style.display = 'none';
        if (pinMap) { pinMap.remove(); pinMap = null; pinMarker = null; }
    }

    function initPinMap() {
        if (pinMap) { pinMap.remove(); pinMap = null; pinMarker = null; }

        var center = savedLat && savedLng ? [savedLat, savedLng] : [12.8797, 121.7740];
        var zoom   = savedLat && savedLng ? 16 : 7;

        pinMap = L.map('pin-map').setView(center, zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(pinMap);

        var pinIcon = L.divIcon({
            html: '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="48" viewBox="0 0 32 42"><path d="M16 0C7.163 0 0 7.163 0 16c0 10.667 16 26 16 26S32 26.667 32 16C32 7.163 24.837 0 16 0z" fill="#2563eb"/><circle cx="16" cy="16" r="7" fill="white"/><circle cx="16" cy="16" r="4" fill="#2563eb"/></svg>',
            className:'', iconSize:[36,48], iconAnchor:[18,48]
        });

        var lat = savedLat || center[0];
        var lng = savedLng || center[1];

        pinMarker = L.marker([lat, lng], { icon: pinIcon, draggable: true }).addTo(pinMap);
        updateCoordsDisplay(lat, lng);

        pinMarker.on('dragend', function(e) {
            var pos = e.target.getLatLng();
            updateCoordsDisplay(pos.lat, pos.lng);
        });

        pinMap.on('click', function(e) {
            pinMarker.setLatLng(e.latlng);
            updateCoordsDisplay(e.latlng.lat, e.latlng.lng);
        });

        // Try GPS auto-center if no saved pin
        if (!savedLat && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                var gLat = pos.coords.latitude;
                var gLng = pos.coords.longitude;
                pinMap.setView([gLat, gLng], 17);
                pinMarker.setLatLng([gLat, gLng]);
                updateCoordsDisplay(gLat, gLng);
            });
        }
    }

    function updateCoordsDisplay(lat, lng) {
        document.getElementById('coords-display').textContent =
            '📍 ' + parseFloat(lat).toFixed(6) + ', ' + parseFloat(lng).toFixed(6);
    }

    function savePin() {
        if (!pinMarker) return;
        var pos = pinMarker.getLatLng();
        var btn = document.getElementById('save-pin-btn');
        btn.textContent = 'Saving...';
        btn.disabled = true;

        fetch('{{ route('portal.location.save') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ latitude: pos.lat, longitude: pos.lng })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                savedLat = pos.lat;
                savedLng = pos.lng;
                closePinModal();
                location.reload();
            }
        })
        .catch(() => {
            btn.textContent = '💾 Save Pin';
            btn.disabled = false;
        });
    }

    document.getElementById('pin-modal').addEventListener('click', function(e) {
        if (e.target === this) closePinModal();
    });
</script>
@endsection
