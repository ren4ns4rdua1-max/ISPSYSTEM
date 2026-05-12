@extends('client.layout')
@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')
@section('page-subtitle', 'Submit and track your support requests')

@section('content')
<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">

    {{-- Ticket List --}}
    <div style="background:white;border-radius:18px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #f8fafc;">
            <h3 class="font-display" style="font-size:15px;font-weight:700;color:#0f172a;">🎫 My Tickets</h3>
        </div>
        @if($tickets->isEmpty())
            <div style="padding:48px;text-align:center;"><p style="font-size:14px;color:#94a3b8;">No tickets submitted yet.</p></div>
        @else
            <div style="display:flex;flex-direction:column;gap:0;">
                @foreach($tickets as $ticket)
                <div style="padding:18px 24px;border-bottom:1px solid #f8fafc;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:8px;">
                        <div>
                            <p style="font-size:14px;font-weight:700;color:#0f172a;">{{ $ticket->subject }}</p>
                            <p style="font-size:12px;color:#64748b;margin-top:2px;">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div style="display:flex;gap:6px;flex-shrink:0;">
                            <span class="{{ $ticket->status_color }}" style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;border:1px solid;display:inline-block;">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
                            <span style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:#f1f5f9;color:#475569;display:inline-block;">{{ ucfirst($ticket->priority) }}</span>
                        </div>
                    </div>
                    <p style="font-size:13px;color:#475569;line-height:1.6;">{{ Str::limit($ticket->message, 120) }}</p>
                    @if($ticket->admin_reply)
                    <div style="margin-top:10px;padding:12px 14px;background:#f0fdf4;border-radius:10px;border-left:3px solid #059669;">
                        <p style="font-size:11px;font-weight:700;color:#059669;margin-bottom:4px;">Admin Reply · {{ $ticket->replied_at?->format('M d, Y') }}</p>
                        <p style="font-size:13px;color:#065f46;">{{ $ticket->admin_reply }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @if($tickets->hasPages())
            <div style="padding:14px 20px;border-top:1px solid #f8fafc;">{{ $tickets->links() }}</div>
            @endif
        @endif
    </div>

    {{-- New Ticket Form --}}
    <div style="background:white;border-radius:18px;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.03);padding:22px;">
        <h3 class="font-display" style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:16px;">➕ New Support Request</h3>
        <form method="POST" action="{{ route('portal.tickets.store') }}">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Subject</label>
                <input type="text" name="subject" required placeholder="Brief description of your issue" value="{{ old('subject') }}"
                       style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box;">
                @error('subject')<p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Priority</label>
                <select name="priority" style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Message</label>
                <textarea name="message" required rows="5" placeholder="Describe your issue in detail..."
                          style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;resize:vertical;box-sizing:border-box;">{{ old('message') }}</textarea>
                @error('message')<p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <button type="submit" style="width:100%;padding:12px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border:none;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;">
                Submit Ticket
            </button>
        </form>
    </div>
</div>
@endsection
