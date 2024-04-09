<?php

use App\Http\Controllers\API\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/events', [EventController::class, 'getAll']);
Route::get('/events/fullcalendar', [EventController::class, 'getAllForFullCalendar']);
Route::get('/events/{id}', [EventController::class, 'getById']);