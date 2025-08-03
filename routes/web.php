<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'sendCode'])->name('auth.send-code');
    Route::get('/verify', [AuthController::class, 'showVerifyForm'])->name('auth.verify-form');
    Route::post('/verify', [AuthController::class, 'verifyCode'])->name('auth.verify-code');
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware('auth:client')->prefix('client')->group(function () {
    Route::get('/dashboard', [BookingController::class, 'clientDashboard'])->name('client.dashboard');
    Route::get('/bookings', [BookingController::class, 'clientBookings'])->name('client.bookings');
    Route::post('/bookings', [BookingController::class, 'create'])->name('booking.create');
});

Route::middleware('auth:master')->prefix('master')->group(function () {
    Route::get('/dashboard', [MasterController::class, 'dashboard'])->name('master.dashboard');
    Route::get('/bookings', [MasterController::class, 'bookings'])->name('master.bookings');
    Route::post('/bookings', [MasterController::class, 'createManualBooking'])->name('master.bookings.create');
    Route::get('/schedules', [MasterController::class, 'schedules'])->name('master.schedules');
    Route::post('/schedules', [MasterController::class, 'storeSchedule'])->name('master.schedules.store');
});

Route::middleware('auth:web')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});
