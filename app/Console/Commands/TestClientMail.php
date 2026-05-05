<?php

namespace App\Console\Commands;

use App\Mail\ClientApprovedMail;
use App\Models\Client;
use App\Models\Technician;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestClientMail extends Command
{
    protected $signature   = 'mail:test-client';
    protected $description = 'Test ClientApprovedMail with a real client and technician';

    public function handle(): void
    {
        $client     = Client::first();
        $technician = Technician::first();

        if (!$client) {
            $this->error('No clients found in database.');
            return;
        }

        $this->info('Client  : ' . $client->name . ' <' . $client->email . '>');
        $this->info('Technician: ' . ($technician ? $technician->name : 'none (approve-only test)'));
        $this->info('Sending email...');

        try {
            Mail::to($client->email)
                ->send(new ClientApprovedMail($client, $technician));

            $this->info('✓ SUCCESS — Email sent to ' . $client->email);
            $this->info('  Check the inbox and also storage/logs/laravel.log for confirmation.');
        } catch (\Exception $e) {
            $this->error('✗ FAILED — ' . $e->getMessage());
            \Log::error('TestClientMail failed: ' . $e->getMessage());
        }
    }
}
