<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ticket Resolved</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:20px;">
    <div style="max-width:640px; margin:0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:20px;">
        <h2 style="margin:0 0 10px; color:#0f172a;">Support Ticket Resolved ✅</h2>
        <p style="margin:0 0 14px; color:#475569; font-size:14px;">
            Hi <strong>{{ $client->name }}</strong>, your support ticket has been marked as resolved.
        </p>

        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px; margin-bottom:14px;">
            <p style="margin:0 0 6px;"><strong>Subject:</strong> {{ $ticket->subject }}</p>
            <p style="margin:0 0 6px;"><strong>Status:</strong> {{ ucfirst(str_replace('_',' ',$ticket->status)) }}</p>
            @if($ticket->solution)
                <p style="margin:10px 0 6px;"><strong>Solution:</strong></p>
                <p style="margin:0; color:#334155; white-space:pre-wrap;">{{ $ticket->solution }}</p>
            @endif
        </div>

        <p style="margin:0 0 18px; color:#475569; font-size:14px;">
            Please log in to the client portal to confirm the resolution.
        </p>

        <p style="margin:0; color:#475569; font-size:12px;">
            Thank you,
            <br />{{ config('app.name') }}
        </p>
    </div>
</body>
</html>

