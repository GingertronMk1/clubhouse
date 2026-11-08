<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/profile', ProfileController::class)->name('profile.show');
});

Route::resource('sports', SportController::class);
Route::resource('sports.positions', PositionController::class)->scoped();
Route::resource('locations', LocationController::class);
Route::resource('clubs', ClubController::class);
Route::resource('clubs.teams', TeamController::class)->scoped();
