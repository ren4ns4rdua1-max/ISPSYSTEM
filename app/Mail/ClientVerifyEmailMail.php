<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ClientVerifyEmailMail extends Mailable
{
    public Client $client;
    public string $verifyUrl;

    public function __construct(Client $client)
    {
        $this->client    = $client;
        $this->verifyUrl = url('/clients/verify-email/' . $client->email_verification_token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verify Your Email — ISP Application');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.client-verify-email');
    }
}
