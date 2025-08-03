<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ScheduleController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'sendCode'])->name('auth.send-code');
    Route::get('/verify', [AuthController::class, 'showVerifyForm'])->name('auth.verify-form');
    Route::post('/verify', [AuthController::class, 'verifyCode'])->name('auth.verify-code');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:client')->prefix('client')->group(function () {
    Route::get('/dashboard', [BookingController::class, 'clientDashboard'])->name('client.dashboard');
    Route::get('/bookings', [BookingController::class, 'clientBookings'])->name('client.bookings');
    Route::post('/bookings', [BookingController::class, 'create'])->name('booking.create');
});

Route::middleware('auth:master')->prefix('master')->group(function () {
    Route::get('/dashboard', [BookingController::class, 'masterDashboard'])->name('master.dashboard');
    Route::get('/bookings', [BookingController::class, 'masterBookings'])->name('master.bookings');
    Route::post('/bookings/manual', [BookingController::class, 'createManual'])->name('booking.create-manual');
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('master.schedules');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('master.schedules.store');
});
