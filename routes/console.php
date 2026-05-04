<?php

use App\Models\AdminNotification;
use App\Models\Client;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Notify admins 1 week before due date (runs daily)
Schedule::call(function () {
    $targetDate = now()->addDays(7)->toDateString();

    Client::where('status', 'active')
        ->whereDate('due_date_time', $targetDate)
        ->each(function (Client $client) {
            // Avoid duplicate notifications on the same day
            $alreadySent = AdminNotification::where('type', AdminNotification::TYPE_PAYMENT_DUE_SOON)
                ->whereDate('created_at', today())
                ->whereJsonContains('data->client_id', $client->id)
                ->exists();

            if (!$alreadySent) {
                AdminNotification::notifyAdmins(
                    AdminNotification::TYPE_PAYMENT_DUE_SOON,
                    'Payment Due in 1 Week',
                    "Client {$client->name} has a payment due on " . $client->due_date_time->format('M d, Y') . '.',
                    ['client_id' => $client->id, 'due_date' => $client->due_date_time->toDateString()]
                );
            }
        });
})->dailyAt('08:00')->name('notify.due-soon');

// Notify admins when a client is overdue (runs daily)
Schedule::call(function () {
    Client::where('status', 'active')
        ->where('due_date_time', '<', now())
        ->each(function (Client $client) {
            // Only send once per day per client
            $alreadySent = AdminNotification::where('type', AdminNotification::TYPE_PAYMENT_OVERDUE)
                ->whereDate('created_at', today())
                ->whereJsonContains('data->client_id', $client->id)
                ->exists();

            if (!$alreadySent) {
                AdminNotification::notifyAdmins(
                    AdminNotification::TYPE_PAYMENT_OVERDUE,
                    'Payment Overdue',
                    "Client {$client->name} has not paid. Due date was " . $client->due_date_time->format('M d, Y') . '.',
                    ['client_id' => $client->id, 'due_date' => $client->due_date_time->toDateString()]
                );
            }
        });
})->dailyAt('08:05')->name('notify.overdue');
