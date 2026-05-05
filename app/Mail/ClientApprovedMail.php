<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Technician;
use App\Models\InstallationJob;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ClientApprovedMail extends Mailable
{
    public Client $client;
    public ?Technician $technician;
    public ?InstallationJob $job;

    public function __construct(Client $client, ?Technician $technician = null, ?InstallationJob $job = null)
    {
        $this->client     = $client;
        $this->technician = $technician;
        $this->job        = $job;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Internet Service Application Has Been Approved! 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-approved',
        );
    }
}
