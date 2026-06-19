<?php

namespace App\Mail;

use App\Models\Technician;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TechnicianVerifyEmailMail extends Mailable
{
    public function __construct(public Technician $technician) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verify Your Email — ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.technician-verify-email');
    }
}
