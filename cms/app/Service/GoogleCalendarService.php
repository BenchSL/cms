<?php

namespace App\Service;

use Google\Exception;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Exception;
use Illuminate\Support\Facades\Log;


class GoogleCalendarService
{
    protected Google_Client $client;
    protected Google_Service_Calendar $calendarService;

    public function __construct(Google_Client $client) {
        $this->client = $client;
        $this->initializeGoogleClient();
        $this->calendarService = new Google_Service_Calendar($this->client);
    }

    /**
     * Initialize the Google client with credentials and configuration.
     */
    protected function initializeGoogleClient(): void {
        $this->client->setAuthConfig(storage_path('credentials.json'));
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->addScope('https://www.googleapis.com/auth/calendar.readonly');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    /**
     * Get the authorization URL to redirect the user to Google.
     *
     * @return string
     */
    public function getAuthUri(): string {
        return $this->client->createAuthUrl();
    }

    /**
     * Fetch calendar events from Google API using the provided access token.
     *
     * @param array $accessToken
     * @return array
     * @throws Exception
     */
    public function fetchCalendarEvents(array $accessToken): array {
        try {
            Log::info('Access Token GoogleCalendarService:', ['token' => $accessToken]);

            $this->setAccessToken($accessToken);
            $calendarId = 'primary';
            $events = $this->calendarService->events->listEvents($calendarId);
            return $events->getItems();
        } catch (Google_Service_Exception $e) {
            throw new Exception('Failed to fetch calendar events: '
                . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred: '
                . $e->getMessage());
        }
    }

    /**
     * Get access token from the authorization code.
     *
     * @param string $authCode
     * @return array
     * @throws Exception
     */
    public function getAccessTokenWithAuthCode(string $authCode): array {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
            if (isset($accessToken['error'])) {
                throw new Exception($accessToken['error_description']);
            }
            return $accessToken;
        } catch (Google_Service_Exception $e) {
            throw new Exception('Failed to get access token: '
                . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred: '
                . $e->getMessage());
        }
    }

    /**
     * Set the access token and refresh it if expired.
     *
     * @param array $accessToken
     * @return void
     * @throws Exception
     */
    protected function setAccessToken(array $accessToken): void {
        $this->client->setAccessToken($accessToken);
        if ($this->client->isAccessTokenExpired()) {
            try {
                $refreshToken = $this->client->getRefreshToken();
                if ($refreshToken) {
                    $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                    session(['google_access_token' => $newAccessToken]);
                } else {
                    throw new Exception('Refresh token is missing. Please re-authenticate.');
                }
            } catch (Exception $e) {
                throw new Exception('Failed to refresh access token: '
                    . $e->getMessage());
            }
        }
    }
}
