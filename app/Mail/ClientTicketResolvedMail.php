<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailable as MailableAlias;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ClientTicketResolvedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Client $client, public SupportTicket $ticket)
    {
    }

    public function build(): self
    {
        return $this->subject('Support Ticket Resolved')
            ->view('emails.ticket-resolved')
            ->with([
                'client' => $this->client,
                'ticket' => $this->ticket,
            ]);
    }
}

