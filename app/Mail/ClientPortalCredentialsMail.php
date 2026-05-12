<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ClientPortalCredentialsMail extends Mailable
{
    public Client $client;
    public string $tempPassword;
    public string $portalUrl;

    public function __construct(Client $client, string $tempPassword)
    {
        $this->client       = $client;
        $this->tempPassword = $tempPassword;
        $this->portalUrl    = url('/portal');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Client Portal Access — ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.client-portal-credentials');
    }
}
