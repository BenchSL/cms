<?php


namespace App\Http\Controllers;

use App\Service\CalendarEventService;
use Illuminate\Http\Request;
use App\Service\GoogleCalendarService;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{
    protected $calendarService;
    protected $calendarEventService;

    public function __construct(GoogleCalendarService $calendarService, CalendarEventService $calendarEventService)
    {
        $this->calendarService = $calendarService;
        $this->calendarEventService = $calendarEventService;
    }

    /**
     * Redirect user to Google for OAuth authentication.
     */
    public function redirectToGoogle()
    {
        $authUrl = $this->calendarService->getAuthUri();
        return redirect($authUrl);
    }

    /**
     * Handle the Google OAuth callback.
     */
    public function handleGoogleCallback(Request $request)
    {
        $authCode = $request->input('code');

        if (!$authCode) {
            return redirect('/')->with('error', 'Authorization failed!');
        }

        try {
            $accessToken = $this->calendarService->getAccessTokenWithAuthCode($authCode);

            session(['google_access_token' => $accessToken]);

            return redirect('/calendar-events')->with('success', 'Google Calendar events fetched successfully!');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Failed to retrieve access token: ' . $e->getMessage());
        }
    }

    /**
     * Fetch events from Google Calendar and store them in the database.
     */
    public function fetchEvents(Request $request)
    {
        try {
            $accessToken = session('google_access_token');
            Log::info('Access Token GoogleCalendarController:', ['token' => $accessToken]);
            if (!$accessToken) {
                return response()->json(['error' => 'Access token not found'], 401);
            }
            $googleEvents = $this->calendarService->fetchCalendarEvents($accessToken);
            $this->calendarEventService->saveGoogleEventsToDb($googleEvents);
            $dbEvents = $this->calendarEventService->getAllEventsFromDb();
            return response()->json(['events' => $dbEvents]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch events: ' . $e->getMessage()], 500);
        }
    }
}
