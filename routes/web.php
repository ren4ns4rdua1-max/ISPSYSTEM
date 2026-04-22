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
    $subscriptionRates = SubscriptionRate::where('is_active', true)
        ->orderBy('monthly_fee', 'asc')
        ->get();
    
    return view('welcome', compact('subscriptionRates'));
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'technician'])->prefix('technician')->name('technician.')->group(function () {
    Route::get('/dashboard', [TechnicianDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Tasks (jobs assigned to this technician)
    Route::get('/tasks', [\App\Http\Controllers\TechnicianJobController::class, 'index'])->name('tasks');
    Route::get('/tasks/{job}', [\App\Http\Controllers\TechnicianJobController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{job}/start', [\App\Http\Controllers\TechnicianJobController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{job}/complete', [\App\Http\Controllers\TechnicianJobController::class, 'complete'])->name('tasks.complete');
    Route::post('/tasks/{job}/update-status', [\App\Http\Controllers\TechnicianJobController::class, 'updateStatus'])->name('tasks.updateStatus');
    
    // History
    Route::get('/history', [\App\Http\Controllers\TechnicianJobController::class, 'history'])->name('history');
});

Route::middleware('auth')->group(function () {
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
});


require __DIR__.'/auth.php';



