<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $table = 'calendar_events';

    protected $fillable = [
        'designation',
        'title',
        'description',
        'event_date',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];
}
