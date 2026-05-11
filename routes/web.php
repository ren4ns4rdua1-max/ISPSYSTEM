<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionRateController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TechnicianDashboardController;
use App\Models\SubscriptionRate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $subscriptionRates = \App\Models\SubscriptionRate::where('is_active', true)
        ->orderBy('monthly_fee', 'asc')
        ->get();

    // Load all welcome settings as a keyed array
    $s = \App\Models\Setting::where('group', 'welcome')
        ->get()
        ->keyBy('key')
        ->map(fn($item) => $item->value);

    // Clear cache so fresh values always load
    \Illuminate\Support\Facades\Cache::flush();

    return view('welcome', compact('subscriptionRates', 's'));
});

// Guest client registration route (from welcome page "Apply" button)
Route::post('/clients/guest', [ClientController::class, 'storeGuest'])->name('clients.storeGuest');
Route::get('/clients/verify-email/{token}', [ClientController::class, 'verifyEmail'])->name('clients.verifyEmail');

// Contact form (public)
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/technician/dashboard', [TechnicianDashboardController::class, 'dashboard'])->name('technician.dashboard');
    Route::get('/technician/tasks', [TechnicianDashboardController::class, 'tasks'])->name('technician.tasks');
    Route::get('/technician/history', [TechnicianDashboardController::class, 'history'])->name('technician.history');
    Route::post('/technician/jobs/{job}/start', [TechnicianDashboardController::class, 'startJob'])->name('technician.jobs.start');
    Route::post('/technician/jobs/{job}/complete', [TechnicianDashboardController::class, 'completeJob'])->name('technician.jobs.complete');
});

Route::middleware('auth')->group(function () {
    // Admin Notifications Routes
    Route::get('/notifications', [\App\Http\Controllers\AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [\App\Http\Controllers\AdminNotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\AdminNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [\App\Http\Controllers\AdminNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\AdminNotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\AdminNotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::get('/admin/templates', [\App\Http\Controllers\Admin\TemplateController::class, 'index'])->name('admin.templates');
    Route::post('/admin/templates', [\App\Http\Controllers\Admin\TemplateController::class, 'update'])->name('admin.templates.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Management Routes
    Route::resource('users', UserController::class);
    
    // Subscription Rates Routes
    Route::resource('subscription-rates', SubscriptionRateController::class);
    
    // Sales Routes - Creates client AND billing together
    Route::resource('sales', SalesController::class);
    Route::post('sales/quick-activate', [SalesController::class, 'quickActivate'])->name('sales.quickActivate');
    
// Client Management Routes
    // Specific routes must come BEFORE resource routes to avoid conflicts
    Route::get('/clients/pending', [ClientController::class, 'pending'])->name('clients.pending');
    Route::get('/clients/map-data', [ClientController::class, 'mapData'])->name('clients.mapData');
    Route::get('/clients/{client}/email-preview', [ClientController::class, 'emailPreview'])->name('clients.emailPreview');
    Route::post('/clients/{client}/approve', [ClientController::class, 'approve'])->name('clients.approve');
    Route::post('/clients/{client}/reject', [ClientController::class, 'reject'])->name('clients.reject');
    Route::post('/clients/{client}/approve-and-assign', [ClientController::class, 'approveAndAssign'])->name('clients.approveAndAssign');
    Route::get('/clients/technicians', [ClientController::class, 'getTechnicians'])->name('clients.getTechnicians');
    Route::resource('clients', ClientController::class);
    
    // Billing Routes
    Route::resource('billings', BillingController::class);
    Route::post('billings/{billing}/mark-paid', [BillingController::class, 'markAsPaid'])->name('billings.markAsPaid');
    Route::get('billings/client/{client}', [BillingController::class, 'getClientDetails'])->name('billings.client-details');
    
    // Payment Routes
    Route::resource('payments', PaymentController::class);
    Route::get('payments/client/{client}/bills', [PaymentController::class, 'getClientBills'])->name('payments.client-bills');
    
    // Technician Routes
    Route::resource('technicians', TechnicianController::class);
    Route::get('technicians/jobs', [TechnicianController::class, 'jobs'])->name('technicians.jobs');
    Route::get('technicians/jobs/create', [TechnicianController::class, 'createJob'])->name('technicians.create-job');
    Route::post('technicians/jobs', [TechnicianController::class, 'storeJob'])->name('technicians.store-job');
    Route::post('technicians/jobs/{job}/assign', [TechnicianController::class, 'assignJob'])->name('technicians.assign-job');
    Route::post('technicians/jobs/{job}/start', [TechnicianController::class, 'startJob'])->name('technicians.start-job');
    Route::post('technicians/jobs/{job}/complete', [TechnicianController::class, 'completeJob'])->name('technicians.complete-job');
    Route::post('technicians/jobs/{job}/cancel', [TechnicianController::class, 'cancelJob'])->name('technicians.cancel-job');
    
    // Reports Routes
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');

    // Contact Messages (admin)
    Route::get('/contact-messages', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact-messages/{message}/read', [\App\Http\Controllers\ContactController::class, 'markRead'])->name('contact.markRead');
    Route::delete('/contact-messages/{message}', [\App\Http\Controllers\ContactController::class, 'destroy'])->name('contact.destroy');
});


require __DIR__.'/auth.php';



