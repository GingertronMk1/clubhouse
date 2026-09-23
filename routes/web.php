<?php

use App\Http\Controllers\Auth\LoginAttemptController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::name('login.')->prefix('/login')->group(function () {
    Route::get('/', \App\Http\Controllers\LoginPageController::class)->name('form');
    Route::post('/', LoginAttemptController::class)->name('attempt');
});
