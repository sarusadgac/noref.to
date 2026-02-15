<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('my', 'pages::my-links')->name('my-links');
});

Route::livewire('report', 'pages::report')->name('report');
Route::livewire('privacy', 'pages::privacy')->name('privacy');
Route::livewire('terms', 'pages::terms')->name('terms');
Route::livewire('about', 'pages::about')->name('about');
Route::livewire('integrate', 'pages::integrate')->name('integrate');

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::livewire('/', 'pages::admin.dashboard')->name('admin.dashboard');
    Route::livewire('links', 'pages::admin.links')->name('admin.links');
    Route::livewire('reports', 'pages::admin.reports')->name('admin.reports');
    Route::livewire('users', 'pages::admin.users')->name('admin.users');
    Route::livewire('domains', 'pages::admin.domains')->name('admin.domains');
    Route::livewire('pulse', 'pages::admin.pulse')->name('admin.pulse');
});

require __DIR__.'/settings.php';

Route::get('{hash}', RedirectController::class)
    ->where('hash', '[A-Za-z0-9]{6}')
    ->middleware('throttle:30,1')
    ->name('redirect');
