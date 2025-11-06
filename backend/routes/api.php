<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SeatController;
use Illuminate\Support\Facades\Route;

Route::apiResource('movies', MovieController::class)->only(['index']);
Route::apiResource('schedules', ScheduleController::class)->only(['index']);

Route::get('schedules/{schedule}/seats', [SeatController::class, 'seatsBySchedule']);
Route::get('schedules/{schedule}/taken-seats', [SeatController::class, 'takenSeats']);

Route::get('cart', [CartController::class, 'getCart']);
Route::post('cart', [CartController::class, 'updateCart']);
Route::post('cart/items', [CartController::class, 'updateCartItems']);

Route::post('orders', [OrderController::class, 'store']);
