<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\SportController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::resource('sports', SportController::class);
Route::resource('sports.positions', PositionController::class)->scoped();
Route::resource('locations', LocationController::class);
Route::resource('clubs', ClubController::class);
