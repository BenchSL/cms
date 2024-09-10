<?php

use App\Http\Controllers\CalendarEventController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleCalendarController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [GoogleCalendarController::class, 'redirectToGoogle']);

Route::get('/auth/google', [GoogleCalendarController::class, 'redirectToGoogle']);

Route::get('/auth/google/callback', [GoogleCalendarController::class, 'handleGoogleCallback']);

Route::get('/calendar-events', [CalendarEventController::class, 'index']);

Route::get('/api/calendar-events', [GoogleCalendarController::class, 'fetchEvents']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
