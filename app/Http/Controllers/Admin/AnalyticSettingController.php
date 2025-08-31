<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnalyticSetting;
class AnalyticSettingController extends Controller
{
         public function index()
    {
        // Fetch all relevant settings and pass them to the view
        $settings = AnalyticSetting::whereIn('key', [
            'facebook_pixel_status',
            'facebook_pixel_id',
            'google_analytics_status',
            'google_analytics_tracking_id',
            'facebook_access_token',
            'facebook_test_event_code',
            'facebook_domain_verification_code'
        ])->pluck('value', 'key');

        return view('admin.setting.analytics', compact('settings'));
    }

    public function update(Request $request)
    {
        // Handle boolean toggles that might not be in the request if unchecked
        AnalyticSetting::updateOrCreate(
            ['key' => 'facebook_pixel_status'],
            ['value' => $request->has('facebook_pixel_status') ? 1 : 0]
        );
        AnalyticSetting::updateOrCreate(
            ['key' => 'google_analytics_status'],
            ['value' => $request->has('google_analytics_status') ? 1 : 0]
        );

        // Loop through and update all other settings from the request
        foreach ($request->except(['_token', 'facebook_pixel_status', 'google_analytics_status']) as $key => $value) {
            AnalyticSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

}
