<?php

namespace App\Console\Commands;

use App\Service\GoogleCalendarService;
use Illuminate\Console\Command;

class FetchCalendarEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Google Calendar events';

    protected $calendarService;

    public function __construct(GoogleCalendarService $calendarService) {
        parent::__construct();
        $this->calendarService = $calendarService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $events = $this -> calendarService -> fetchCalendarEvents('test');

        if (empty($events)) {
            $this -> info("No events found.");
        } else {
            foreach ($events as $event) {
                $this -> info("Event: " . $event -> getSummary());
                $this -> info("Date:" . $event -> getStart() -> getDate());
            }
        }
        return 0;
    }
}
