<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentApprovedMail extends Mailable
{
    public Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Approved — ' . $this->payment->receipt_number,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-approved');
    }
}
