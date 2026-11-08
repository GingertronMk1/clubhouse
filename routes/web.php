<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::resource('sports', SportController::class);
Route::resource('sports.positions', PositionController::class)->scoped();
Route::resource('locations', LocationController::class);
Route::resource('clubs', ClubController::class);
Route::resource('clubs.teams', TeamController::class)->scoped();
