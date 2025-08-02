<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use DateTime;
use DateTimeZone;

class GoogleCalendarService
{
    protected $client;
    protected $calendarService;
    protected $calendarTimeZone = 'Asia/Makassar';

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('PN Global Booking System');
        $this->client->setScopes(Google_Service_Calendar::CALENDAR);
        $this->client->setAuthConfig(WRITEPATH . 'google/credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
        $this->client->setRedirectUri(BASE_URL . 'oauth2callback');

        // Load previously authorized token from a file, if it exists.
        $tokenPath = WRITEPATH . 'google/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        // Refresh the token if it's expired.
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
            } else {
                // Token sudah expired dan tidak ada refresh token
                log_message('error', 'Google Calendar token expired and no refresh token available');
                throw new \RuntimeException('Google Calendar authentication failed. Please re-authenticate.');
            }
        }

        $this->calendarService = new Google_Service_Calendar($this->client);
    }

    public function createEvent($calendarId, $eventData)
    {
        try {
            $event = new Google_Service_Calendar_Event($eventData);
            return $this->calendarService->events->insert($calendarId, $event);
        } catch (\Google\Service\Exception $e) {
            log_message('error', 'Error creating event: ' . $e->getMessage());
            throw new \RuntimeException('Error creating event: ' . $e->getMessage());
        }
    }

    public function getAvailableSlots($calendarId, $timeMin, $timeMax, $userTimeZone)
    {
        $events = $this->calendarService->events->listEvents($calendarId, [
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ]);

        $availableSlots = [];

        // Define the fixed time slots for each day
        $fixedSlots = [
            ['08:00:00', '09:00:00'],
            ['09:30:00', '10:30:00'],
            ['11:00:00', '12:00:00'],
            ['14:00:00', '15:00:00'],
            ['15:30:00', '16:30:00'],
            ['17:00:00', '18:00:00'],
        ];

        // Create DateTime objects for the start (timeMin) and end (timeMax) range
        $currentDate = new DateTime($timeMin, new DateTimeZone($this->calendarTimeZone));
        $endDate = new DateTime($timeMax, new DateTimeZone($this->calendarTimeZone));

        // Loop through each day within the time range (week)
        while ($currentDate < $endDate) {
            foreach ($fixedSlots as $slot) {
                // Create start and end times for each slot
                $slotStart = clone $currentDate;
                $slotStart->setTime(...explode(':', $slot[0]));

                $slotEnd = clone $slotStart;
                $slotEnd->setTime(...explode(':', $slot[1]));

                // Convert to RFC3339 format for comparison
                $slotStartStr = $slotStart->format(DateTime::RFC3339);
                $slotEndStr = $slotEnd->format(DateTime::RFC3339);

                $slotAvailable = true;

                foreach ($events->getItems() as $event) {
                    $eventStart = strtotime($event->start->dateTime);
                    $eventEnd = strtotime($event->end->dateTime);

                    $slotStartTimestamp = strtotime($slotStartStr);
                    $slotEndTimestamp = strtotime($slotEndStr);

                    // Check if the current slot overlaps with any existing event
                    if (($slotStartTimestamp >= $eventStart && $slotStartTimestamp < $eventEnd) ||
                        ($slotEndTimestamp > $eventStart && $slotEndTimestamp <= $eventEnd)) {
                        $slotAvailable = false;
                        break;
                    }
                }

                if ($slotAvailable) {
                    // Convert slot times to user timezone and format as d-m-Y H:i:s
                    $slotStart->setTimezone(new DateTimeZone($userTimeZone));
                    $slotEnd->setTimezone(new DateTimeZone($userTimeZone));

                    $availableSlots[] = [
                        'start' => $slotStart->format('d-m-Y H:i:s'),
                        'end' => $slotEnd->format('d-m-Y H:i:s'),
                    ];
                }
            }

            // Move to the next day
            $currentDate->modify('+1 day');
        }

        return $availableSlots;
    }



    public function getSlotsNextDay($calendarId, $userTimeZone)
    {
        $currentDateTime = new DateTime('now', new DateTimeZone($this->calendarTimeZone));
        $nextDay = (clone $currentDateTime)->modify('+1 day')->setTime(0, 0);
        $endWeek = (clone $nextDay)->modify('+1 week');

        $timeMin = $nextDay->format(DateTime::RFC3339);
        $timeMax = $endWeek->format(DateTime::RFC3339);

        return $this->getAvailableSlots($calendarId, $timeMin, $timeMax, $userTimeZone);
    }
}
