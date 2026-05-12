<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\InstallationJob;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Str;

class ClientJobCompletedMail extends Mailable
{
    public Client $client;
    public InstallationJob $job;
    public string $portalUrl;
    public ?string $tempPassword;
    public string $magicLoginUrl;

    public function __construct(Client $client, InstallationJob $job)
    {
        $this->client       = $client;
        $this->job          = $job;
        $this->portalUrl    = url('/portal');
        $this->tempPassword = $client->portal_temp_password;

        // Generate a one-time magic login token valid for 7 days
        $token = Str::random(64);
        $client->update([
            'magic_login_token'      => $token,
            'magic_token_expires_at' => now()->addDays(7),
        ]);
        $this->magicLoginUrl = url('/portal/magic-login/' . $token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: '✅ Your Internet Service is Now Active — ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.client-job-completed');
    }
}
