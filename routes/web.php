<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

Route::get('/event/create', [EventController::class, '__create'])->name('events.create.form');
Route::get('/events/{id}/edit', [EventController::class, '__edit'])->name('events.edit.form');
Route::post('/events', [EventController::class, 'store'])->name('events.store');
Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
Route::put('/events/{id}/move', [EventController::class, 'move'])->name('events.move');
Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');

Route::get('/', function () {
    return view('dashboard.index');
});

