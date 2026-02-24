<?php
namespace Modules\Zoom\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ZoomController extends Controller
{
    public function createMeeting(Request $request)
    {
        // Get module configuration
        $app = \App\Models\ExternalApp::where('slug', 'zoom')->first();
        
        if (!$app || !$app->is_enabled) {
            return response()->json(['error' => 'Module not available'], 403);
        }

        $service = new \Modules\Zoom\Services\ZoomMeetingService();
        $meetingData = $service->createMeeting(
            $request->input('topic', 'Test Meeting'),
            $request->input('start_time', now()->addHour()->toIso8601String()),
            $request->input('duration', 60),
            $request->input('timezone', 'UTC')
        );

        if (!$meetingData) {
            return response()->json(['error' => 'Failed to create meeting'], 500);
        }

        return response()->json($meetingData);
    }

    public function testConnection(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $accountId = $request->input('ZOOM_ACCOUNT_ID');
        $clientId = $request->input('ZOOM_CLIENT_ID');
        $clientSecret = $request->input('ZOOM_CLIENT_SECRET');

        if (empty($accountId) || empty($clientId) || empty($clientSecret)) {
            return response()->json(['success' => false, 'message' => 'Please provide Account ID, Client ID, and Client Secret.'], 400);
        }

        $service = new \Modules\Zoom\Services\ZoomMeetingService();
        $success = $service->testConnection($accountId, $clientId, $clientSecret);

        if ($success) {
            return response()->json(['success' => true, 'message' => 'Successfully connected to Zoom API!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to connect to Zoom API. Please check your credentials and try again.'], 400);
        }
    }
}
?>