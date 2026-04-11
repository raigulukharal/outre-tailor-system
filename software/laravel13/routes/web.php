<?php
// routes/web.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReminderController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// ============================================
// ROOT REDIRECT - Redirect / to login or dashboard
// ============================================
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ============================================
// GUEST ROUTES (Not logged in)
// ============================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ============================================
// LOGOUT ROUTE
// ============================================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================
// AUTO LOGIN FROM DIRECT-LOGIN.PHP
// ============================================
Route::get('/autologin/{id}', function ($id) {
    $user = User::find($id);
    if ($user) {
        Auth::login($user);
        session()->regenerate();
    }
    return redirect()->route('dashboard');
})->name('autologin');

// ============================================
// AUTHENTICATED ROUTES (Logged in)
// ============================================
Route::middleware('auth')->group(function () {
    
    // Dashboard with auto-login support
    Route::get('/dashboard', function () {
        if (request()->has('autologin') && !Auth::check()) {
            $user = User::find(request()->autologin);
            if ($user) {
                Auth::login($user);
                session()->regenerate();
            }
        }
        return view('dashboard.index');
    })->name('dashboard');
    
    // Order Management
    Route::get('/orders/create', function () {
        return view('orders.create');
    })->name('orders.create');
    
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
    
    // Reminders
    Route::get('/reminders', function () {
        return view('reminders.index');
    })->name('reminders');
    
    Route::get('/reminders/fetch', [ReminderController::class, 'reminders'])->name('reminders.fetch');
    
    // Completed Orders Management
    Route::get('/completed-orders', [OrderController::class, 'completedOrdersView'])->name('completed-orders');
    Route::get('/completed-orders/fetch', [OrderController::class, 'fetchCompletedOrders'])->name('completed-orders.fetch');
    Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
});

// ============================================
// FALLBACK ROUTE (404 handler)
// ============================================
Route::fallback(function () {
    return response()->json(['error' => 'Page not found'], 404);
});