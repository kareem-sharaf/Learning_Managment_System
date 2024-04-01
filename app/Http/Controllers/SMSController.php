<?php

namespace App\Http\Controllers;

use App\Services\TwilioService;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function sendSMS(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required',
            'message' => 'required',
        ]);

        $response = $this->twilioService->sendSMS($request->phone, $request->message);

        if ($response->sid) {
            return response()->json(['message' => true, 'message' => 'SMS sent successfully.']);
        } else {

            return response()->json(['message' => false, 'message' => 'Failed to send SMS.']);
        }
    }
}
