@extends('client.layout')
@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')
@section('page-subtitle', 'Update your personal information and password')

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">

    {{-- Profile Info --}}
    <div style="background:white;border-radius:18px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);padding:24px;">
        <h3 class="font-display" style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:20px;">👤 Personal Information</h3>

        {{-- Avatar --}}
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding:16px;background:#f8fafc;border-radius:14px;">
            @if(Auth::user()->photo)
                <img src="{{ asset('storage/'.Auth::user()->photo) }}" style="width:64px;height:64px;border-radius:16px;object-fit:cover;flex-shrink:0;">
            @else
                <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#dc2626,#f97316);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:24px;flex-shrink:0;">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
            @endif
            <div>
                <p style="font-size:15px;font-weight:700;color:#0f172a;">{{ Auth::user()->name }}</p>
                <p style="font-size:12px;color:#64748b;">{{ Auth::user()->email }}</p>
                <p style="font-size:11px;color:#94a3b8;margin-top:2px;">Account #{{ str_pad($client->id,6,'0',STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('portal.profile.update') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Full Name</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                       style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box;">
                @error('name')<p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone_number) }}"
                       style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Profile Photo</label>
                <input type="file" name="photo" accept="image/*"
                       style="width:100%;padding:10px 12px;border:1.5px dashed #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;background:#f9fafb;box-sizing:border-box;">
            </div>
            <button type="submit" style="width:100%;padding:12px;background:linear-gradient(135deg,#dc2626,#b91c1c);color:white;border:none;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;">
                Save Changes
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div style="background:white;border-radius:18px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);padding:24px;">
        <h3 class="font-display" style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:20px;">🔒 Change Password</h3>
        <form method="POST" action="{{ route('portal.password.update') }}">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Current Password</label>
                <input type="password" name="current_password" required
                       style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box;">
                @error('current_password')<p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">New Password</label>
                <input type="password" name="password" required
                       style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box;">
                @error('password')<p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Confirm New Password</label>
                <input type="password" name="password_confirmation" required
                       style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box;">
            </div>
            <button type="submit" style="width:100%;padding:12px;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:white;border:none;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;">
                Change Password
            </button>
        </form>

        {{-- Account Info (read-only) --}}
        <div style="margin-top:24px;padding-top:20px;border-top:1px solid #f1f5f9;">
            <h4 style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px;">Account Details</h4>
            @foreach([
                ['Email', Auth::user()->email],
                ['PPPoE Username', $client->pppoe_name],
                ['Barangay', $client->barangay],
                ['NAP Box', $client->nap_box],
            ] as [$label, $value])
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f8fafc;">
                <span style="font-size:12px;color:#94a3b8;font-weight:600;">{{ $label }}</span>
                <span style="font-size:12px;color:#475569;font-weight:600;">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
