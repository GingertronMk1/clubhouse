<?php

use App\Http\Controllers\Auth\LoginAttemptController;
use App\Http\Controllers\Auth\LoginPageController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

Route::inertia('/', 'Welcome')->name('home');

Route::name('auth.')
    ->prefix('/')
    ->group(function () {
        Route::middleware(RedirectIfAuthenticated::class)->group(function () {
            Route::get('/login', LoginPageController::class)->name('login.show');
            Route::post('/login', LoginAttemptController::class)->name('login.create');
        });
        Route::middleware(Authenticate::class)->group(function () {
            Route::post('/logout', LogoutController::class)->name('logout');
        });
    });
