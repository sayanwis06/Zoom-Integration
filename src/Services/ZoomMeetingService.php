<?php

namespace Modules\Zoom\Services;

use App\Services\ExternalApps\ExternalAppService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomMeetingService
{
    private $accountId;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->accountId = ExternalAppService::staticGetModuleEnv('zoom', 'ZOOM_ACCOUNT_ID');
        $this->clientId = ExternalAppService::staticGetModuleEnv('zoom', 'ZOOM_CLIENT_ID');
        $this->clientSecret = ExternalAppService::staticGetModuleEnv('zoom', 'ZOOM_CLIENT_SECRET');
    }

    private function getAccessToken()
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $this->accountId,
                ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Zoom API Token Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Zoom API Token Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function createMeeting($topic, $startTime, $duration, $timezone)
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return null;
        }

        try {
            $response = Http::withToken($token)
                ->post("https://api.zoom.us/v2/users/me/meetings", [
                    'topic'      => $topic,
                    'type'       => 2, // Scheduled meeting
                    'start_time' => \Carbon\Carbon::parse($startTime)->format('Y-m-d\TH:i:s\Z'),
                    'duration'   => (int) $duration,
                    'timezone'   => $timezone,
                    'settings'   => [
                        'join_before_host'  => true,
                        'mute_upon_entry'   => true,
                        'waiting_room'      => false,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'id'       => $data['id'],
                    'join_url' => $data['join_url'],
                    'host_url' => $data['start_url'],
                ];
            }

            Log::error('Zoom API Create Meeting Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Zoom API Create Meeting Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function testConnection($accountId, $clientId, $clientSecret)
    {
        try {
            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $accountId,
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Zoom API Test Connection Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Zoom API Test Connection Exception: ' . $e->getMessage());
            return false;
        }
    }
}
