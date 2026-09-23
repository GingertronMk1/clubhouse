<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::post('/login', LoginController::class)->name('login');
