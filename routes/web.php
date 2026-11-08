<?php

use App\Http\Controllers\PositionController;
use App\Http\Controllers\SportController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::resource('sports', SportController::class)->except('show');

Route::resource('sports.positions', PositionController::class)->except('show')->scoped();
