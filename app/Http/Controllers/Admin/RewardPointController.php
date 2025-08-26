<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardPointSetting;
use App\Models\Customer;
use App\Models\RewardPoint;
use Illuminate\Http\Request;

class RewardPointController extends Controller
{
    // Show the settings page
    public function settings()
    {
        $settings = RewardPointSetting::first();
        return view('admin.reward.settings', compact('settings'));
    }

    // Update the settings
    public function updateSettings(Request $request)
    {
        $request->validate([
            'earn_points_per_unit' => 'required|integer|min:1',
            'earn_per_unit_amount' => 'required|numeric|min:1',
            'redeem_points_per_unit' => 'required|integer|min:1',
            'redeem_per_unit_amount' => 'required|numeric|min:1',
        ]);

        $settings = RewardPointSetting::first();
        $settings->update($request->all());

        return redirect()->back()->with('success', 'Reward point settings updated successfully.');
    }

    public function history()
{
    // This function now only needs to load the main view.
    return view('admin.reward.history');
}

// Add this new function to handle AJAX data requests
public function data(Request $request)
{
    $query = Customer::withCount('rewardPointLogs');

    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('phone', 'LIKE', "%{$searchTerm}%");
    }

    $customers = $query->latest()->paginate(15);
    return response()->json($customers);
}

    // Show detailed transaction history for a single customer
    public function customerHistory(Customer $customer)
    {
        $logs = RewardPoint::where('customer_id', $customer->id)->latest()->paginate(20);
        return view('admin.reward.customer_history', compact('customer', 'logs'));
    }
}
