<?php

namespace App\Mail;

use App\Models\Billing;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BillingDueNoticeMail extends Mailable
{
    public Billing $billing;

    public function __construct(Billing $billing)
    {
        $this->billing = $billing;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Due Reminder — ' . $this->billing->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.billing-due-notice',
        );
    }
}
