<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionRateController;
use App\Models\SubscriptionRate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $subscriptionRates = SubscriptionRate::where('is_active', true)
        ->orderBy('monthly_fee', 'asc')
        ->get();
    
    return view('welcome', compact('subscriptionRates'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Management Routes
    Route::resource('users', UserController::class);
    
    // Subscription Rates Routes
    Route::resource('subscription-rates', SubscriptionRateController::class);
});

require __DIR__.'/auth.php';
