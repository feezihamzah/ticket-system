<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Main UI (seat selection page)
Route::get('/', [SeatController::class, 'index']);

// Get seats for selected event
Route::get('/events/{event}/seats', [SeatController::class, 'getSeats']);

// Reserve seats (lock)
Route::post('/events/{event}/reserve', [SeatController::class, 'reserve']);

// Get event inventory summary (IMPORTANT REQUIREMENT)
Route::get('/events/{event}', [EventController::class, 'show']);

// Confirm reservation (simulate payment success)
Route::post('/reservations/{reservation}/confirm', [SeatController::class, 'confirm']);

// Cancel reservation (release seats)
Route::post('/reservations/{reservation}/cancel', [SeatController::class, 'cancel']);