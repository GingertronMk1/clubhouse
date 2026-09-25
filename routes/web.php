<?php

use App\Http\Controllers\SportController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::resource('sports', SportController::class);
