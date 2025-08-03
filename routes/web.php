<?php

use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Auth\MasterAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('client')->name('client.')->group(function () {
    Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [ClientAuthController::class, 'sendCode'])->name('auth.send-code');
    Route::get('/verify', [ClientAuthController::class, 'showVerifyForm'])->name('auth.verify-form');
    Route::post('/verify', [ClientAuthController::class, 'verifyCode'])->name('auth.verify-code');
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('auth.logout');
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [ClientController::class, 'bookings'])->name('bookings');
    Route::get('/projects', [ClientController::class, 'projects'])->name('projects');
    Route::get('/project/{slug}', [ClientController::class, 'showProject'])->name('project');
    Route::post('/project/{slug}/favorite', [ClientController::class, 'addProjectToFavorites'])->name('project.favorite');
    Route::post('/project/{slug}/booking', [ClientController::class, 'createBooking'])->name('project.booking');
    Route::patch('/bookings/{booking}/cancel', [ClientController::class, 'cancelBooking'])->name('bookings.cancel');
});

Route::prefix('master')->name('master.')->group(function () {
    Route::get('/login', [MasterAuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [MasterAuthController::class, 'sendCode'])->name('auth.send-code');
    Route::get('/verify', [MasterAuthController::class, 'showVerifyForm'])->name('auth.verify-form');
    Route::post('/verify', [MasterAuthController::class, 'verifyCode'])->name('auth.verify-code');
    Route::post('/logout', [MasterAuthController::class, 'logout'])->name('auth.logout');
    Route::get('/dashboard', [MasterController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [MasterController::class, 'bookings'])->name('bookings');
    Route::patch('/bookings/{booking}', [MasterController::class, 'updateBooking'])->name('bookings.update');
    Route::get('/projects', [MasterController::class, 'projects'])->name('projects');
    Route::post('/projects', [MasterController::class, 'createProject'])->name('projects.create');
    Route::get('/categories', [MasterController::class, 'categories'])->name('categories');
    Route::post('/categories', [MasterController::class, 'createCategory'])->name('categories.create');
    Route::get('/services', [MasterController::class, 'services'])->name('services');
    Route::post('/services', [MasterController::class, 'createService'])->name('services.create');
    Route::get('/daily-schedules', [MasterController::class, 'dailySchedules'])->name('daily_schedules');
    Route::post('/daily-schedules', [MasterController::class, 'createDailySchedule'])->name('daily_schedules.create');
    Route::patch('/daily-schedules/{schedule}', [MasterController::class, 'updateDailySchedule'])->name('daily_schedules.update');
    Route::get('/daily-schedule-templates', [MasterController::class, 'dailyScheduleTemplates'])->name('daily_schedule_templates');
    Route::post('/daily-schedule-templates', [MasterController::class, 'createDailyScheduleTemplate'])->name('daily_schedule_templates.create');
    Route::post('/daily-schedule-templates/apply', [MasterController::class, 'applyDailyScheduleTemplate'])->name('daily_schedule_templates.apply');
    Route::get('/blacklist', [MasterController::class, 'blacklist'])->name('blacklist');
    Route::post('/blacklist', [MasterController::class, 'addToBlacklist'])->name('blacklist.add');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('auth.login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('auth.logout');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/masters', [AdminController::class, 'masters'])->name('masters');
    Route::get('/clients', [AdminController::class, 'clients'])->name('clients');
    Route::get('/projects', [AdminController::class, 'projects'])->name('projects');
    Route::get('/tariffs', [AdminController::class, 'tariffs'])->name('tariffs');
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
});

Route::get('/', function () {
    return view('welcome');
});
