<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GatewaySetting; // <-- UPDATED to use the new model

class SmsGatewaySettingController extends Controller
{
    /**
     * Display the SMS gateway settings page.
     */
    public function index()
    {
        // UPDATED to use the new model
        $settings = GatewaySetting::pluck('value', 'key')->all();

        $apiKey = $settings['sms_api_key'] ?? '';
        $secretKey = $settings['sms_secret_key'] ?? '';
        $senderId = $settings['sms_sender_id'] ?? '';
        $smsBody = $settings['sms_body'] ?? 'Your OTP is: {otp}';

        return view('admin.setting.sms.index', compact('apiKey', 'secretKey', 'senderId', 'smsBody'));
    }

    /**
     * Store or update the SMS gateway settings.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sms_api_key' => 'nullable|string',
            'sms_secret_key' => 'nullable|string',
            'sms_sender_id' => 'nullable|string',
            'sms_body' => 'nullable|string',
        ]);

        // UPDATED to use the new model
        GatewaySetting::updateOrCreate(['key' => 'sms_api_key'], ['value' => $request->sms_api_key]);
        GatewaySetting::updateOrCreate(['key' => 'sms_secret_key'], ['value' => $request->sms_secret_key]);
        GatewaySetting::updateOrCreate(['key' => 'sms_sender_id'], ['value' => $request->sms_sender_id]);
        GatewaySetting::updateOrCreate(['key' => 'sms_body'], ['value' => $request->sms_body]);

        return redirect()->back()->with('success', 'SMS Gateway settings updated successfully.');
    }
}