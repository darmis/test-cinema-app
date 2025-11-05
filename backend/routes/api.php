<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SeatController;
use Illuminate\Support\Facades\Route;

Route::apiResource('movies', MovieController::class)->only(['index']);
Route::apiResource('schedules', ScheduleController::class)->only(['index']);

Route::get('schedules/{schedule}/seats', [SeatController::class, 'seatsBySchedule']);
