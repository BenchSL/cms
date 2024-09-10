<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GoogleCalendarController;
use Illuminate\Http\Request;

class FetchGoogleCalendarEvents extends Command
{
    protected $signature = 'fetch:google-calendar-events';

    protected $description = 'Fetch Google Calendar events and store them in the database';

    protected $calendarController;

    public function __construct(GoogleCalendarController $calendarController)
    {
        parent::__construct();
        $this->calendarController = $calendarController;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        try {
            $request = new Request();
            $this->calendarController->fetchEvents($request);
            $this->info('Google Calendar events have been fetched successfully.');
        } catch (\Exception $e) {
            $this->error('Error fetching Google Calendar events: ' . $e->getMessage());
        }
    }
}
