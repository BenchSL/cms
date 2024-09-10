<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    public function index()
    {
        return view('calendar-events');
    }
}
