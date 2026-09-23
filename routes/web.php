<?php

use App\Http\Controllers\Auth\LoginAttemptController;
use App\Http\Controllers\LoginPageController;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::name('login.')->prefix('/login')->middleware(RedirectIfAuthenticated::class)->group(function () {
    Route::get('/', LoginPageController::class)->name('form');
    Route::post('/', LoginAttemptController::class)->name('attempt');
});
