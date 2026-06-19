@extends('client.layout')
@section('title', 'Ticket Details')
@section('page-title', 'Ticket Details')
@section('page-subtitle', 'View status, resolution and confirm completion')

@section('content')
<div style="display:grid;grid-template-columns:1fr;gap:20px;">

    <div style="background:white;border-radius:18px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #f8fafc;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div>
                    <h3 style="font-size:14px;font-weight:800;color:#0f172a;margin-bottom:4px;">{{ $ticket->subject }}</h3>
                    <p style="font-size:12px;color:#64748b;">Created: {{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <span class="{{ $ticket->status_color }}" style="font-size:10px;font-weight:800;padding:3px 10px;border-radius:20px;border:1px solid;display:inline-block;">
                        {{ ucfirst(str_replace('_',' ',$ticket->status)) }}
                    </span>
                    <span style="font-size:10px;font-weight:800;padding:3px 10px;border-radius:20px;background:#f1f5f9;color:#475569;display:inline-block;">
                        {{ ucfirst($ticket->priority) }} Priority
                    </span>
                </div>
            </div>
        </div>

        <div style="padding:18px 24px;">
            <p style="font-size:12px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Your Message</p>
            <div style="padding:14px 16px;background:#f8fafc;border:1px solid #f1f5f9;border-radius:14px;">
                <p style="font-size:13px;color:#0f172a;white-space:pre-wrap;line-height:1.7;">{{ $ticket->message }}</p>
            </div>

            @if($ticket->admin_reply)
                <div style="margin-top:18px;">
                    <p style="font-size:12px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Technician / Admin Reply</p>
                    <div style="padding:14px 16px;background:#f0fdf4;border-radius:14px;border-left:4px solid #059669;">
                        <p style="font-size:11px;font-weight:900;color:#059669;margin-bottom:6px;">Replied: {{ $ticket->replied_at?->format('M d, Y') ?? '-' }}</p>
                        <p style="font-size:13px;color:#065f46;white-space:pre-wrap;line-height:1.7;">{{ $ticket->admin_reply }}</p>
                    </div>
                </div>
            @endif

            @if($ticket->troubleshooting_notes)
                <div style="margin-top:18px;">
                    <p style="font-size:12px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Troubleshooting</p>
                    <div style="padding:14px 16px;background:#eff6ff;border-radius:14px;border-left:4px solid #2563eb;">
                        <p style="font-size:13px;color:#1d4ed8;white-space:pre-wrap;line-height:1.7;">{{ $ticket->troubleshooting_notes }}</p>
                    </div>
                </div>
            @endif

            @if($ticket->solution)
                <div style="margin-top:18px;">
                    <p style="font-size:12px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Solution</p>
                    <div style="padding:14px 16px;background:#ecfdf7;border-radius:14px;border-left:4px solid #059669;">
                        <p style="font-size:13px;color:#065f46;white-space:pre-wrap;line-height:1.7;">{{ $ticket->solution }}</p>
                    </div>
                </div>
            @endif

            <div style="margin-top:18px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                @if($ticket->status === 'resolved' && !$ticket->client_confirmed_at)
                    <form method="POST" action="{{ route('portal.tickets.confirmResolution', $ticket) }}">
                        @csrf
                        <button type="submit" style="padding:12px 18px;background:linear-gradient(135deg,#059669,#047857);color:white;border:none;border-radius:14px;font-size:13px;font-weight:800;cursor:pointer;">
                            ✅ Confirm Resolution
                        </button>
                    </form>
                @else
                    <div style="font-size:12px;font-weight:800;color:#475569;">
                        Resolution: 
                        <span style="color:#059669;">{{ $ticket->client_confirmed_at ? 'Confirmed' : ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
                    </div>
                @endif
            </div>

            @if($ticket->resolved_at)
                <p style="margin-top:12px;font-size:11px;color:#64748b;">
                    Resolved: {{ $ticket->resolved_at->format('M d, Y h:i A') }}
                    @if($ticket->client_confirmed_at)
                        · Confirmed: {{ $ticket->client_confirmed_at->format('M d, Y h:i A') }}
                    @endif
                </p>
            @endif

            <div style="margin-top:20px;">
                <a href="{{ route('portal.tickets') }}" style="display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;background:#f1f5f9;color:#0f172a;font-weight:800;font-size:13px;text-decoration:none;border:1px solid #e2e8f0;">
                    ← Back to Tickets
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

