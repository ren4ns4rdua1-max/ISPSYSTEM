<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature   = 'mail:test';
    protected $description = 'Send a test email to verify Gmail SMTP configuration';

    public function handle(): void
    {
        $to = 'a64378267@gmail.com';
        $this->info("Sending test email to {$to} ...");

        try {
            Mail::raw(
                "Hello!\n\nThis is a test email from ISP System.\n\nIf you received this, your Gmail SMTP is configured correctly.\n\n— ISP System",
                function ($m) use ($to) {
                    $m->to($to)->subject('ISP System — Mail Test ✓');
                }
            );
            $this->info('✓ SUCCESS: Email sent! Check your inbox at ' . $to);
        } catch (\Exception $e) {
            $this->error('✗ FAILED: ' . $e->getMessage());
        }
    }
}
