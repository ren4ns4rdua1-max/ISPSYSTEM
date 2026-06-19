<?php

namespace App\Mail;

use App\Models\Technician;
use App\Models\InstallationJob;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TechnicianJobAssignedMail extends Mailable
{
    public function __construct(
        public Technician $technician,
        public InstallationJob $job
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Job Assigned — ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.technician-job-assigned');
    }
}
