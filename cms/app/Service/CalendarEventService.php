<?php

namespace App\Service;

use App\Models\CalendarEvent;
use Illuminate\Support\Collection;

class CalendarEventService
{
    /**
     * Save Google Calendar events to the database.
     *
     * @param array $googleEvents
     * @return void
     */
    public function saveGoogleEventsToDb(array $googleEvents): void
    {
        foreach ($googleEvents as $googleEvent) {
            $startDateTime = $googleEvent['start']['dateTime'] ?? $googleEvent['start']['date'];

            CalendarEvent::updateOrCreate(
                ['designation' => $googleEvent['id']], // Use Google event ID to prevent duplicates
                [
                    'title' => $googleEvent['summary'] ?? 'No title',  // Default title if not present
                    'description' => $googleEvent['description'] ?? 'No description available',
                    'event_date' => $startDateTime,
                ]
            );
        }
    }

    /**
     * Get all calendar events from the database.
     *
     * @return Collection
     */
    public function getAllEventsFromDb(): Collection
    {
        return CalendarEvent::all();
    }

    /**
     * Get events for a specific date range from the database.
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getEventsByDateRange(string $startDate, string $endDate): Collection
    {
        return CalendarEvent::whereBetween('event_date', [$startDate, $endDate])->get();
    }

    /**
     * Delete an event by its designation (ID from Google Calendar).
     *
     * @param string $designation
     * @return bool|null
     */
    public function deleteEventByDesignation(string $designation): ?bool
    {
        return CalendarEvent::where('designation', $designation)->delete();
    }
}
