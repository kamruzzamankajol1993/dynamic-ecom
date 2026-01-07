<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardPointSetting;
use App\Models\Customer;
use App\Models\RewardPoint;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
    // ১. কুয়েরিতে withSum যোগ করা হয়েছে
    $query = Customer::withCount('rewardPointLogs')
        ->withSum(['rewardPointLogs as total_earned' => function ($q) {
            $q->where('type', 'earned');
        }], 'points')
        ->withSum(['rewardPointLogs as total_redeemed' => function ($q) {
            $q->where('type', 'redeemed');
        }], 'points');

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


    public function generateHistoricalPoints(Request $request)
{
    // --- TIME LIMIT UPDATE ---
    // Execution time আনলিমিটেড করা হলো যাতে ১০,০০০+ ডাটা প্রসেস করতে Timeout না হয়
    set_time_limit(0); 
    // মেমোরি লিমিটও বাড়িয়ে নেওয়া ভালো (যদি সার্ভার পারমিশন দেয়)
    ini_set('memory_limit', '-1'); 
    // -------------------------

    $request->validate([
        'date' => 'required|date',
    ]);

    $targetDate = Carbon::parse($request->date)->endOfDay();
    $settings = RewardPointSetting::first();

    // ১. সেটিংস চেক করা
    if (!$settings || !$settings->is_enabled) {
        return redirect()->back()->with('error', 'Reward system is disabled or settings not found.');
    }

    try {
        $count = 0;
        $totalPoints = 0;

        DB::transaction(function () use ($targetDate, $settings, &$count, &$totalPoints) {
            
            // ২. যেই অর্ডারগুলোর অলরেডি পয়েন্ট জেনারেট করা আছে, তাদের ID বের করা
            $existingOrderIds = RewardPoint::where('type', 'earned')
                                           ->whereNotNull('order_id')
                                           ->pluck('order_id')
                                           ->toArray();

            // ৩. এলিজিবল অর্ডার খুঁজে বের করা (Delivered + Date Range + Not in Existing)
            // chunk(100) ব্যবহার করা হয়েছে যাতে মেমোরি লোড না পড়ে
            Order::where('status', 'delivered')
                 ->where('created_at', '<=', $targetDate)
                 ->whereNotIn('id', $existingOrderIds) // ডুপ্লিকেট আটকানোর প্রধান চেক
                 ->chunk(100, function ($orders) use ($settings, &$count, &$totalPoints) {
                     
                     foreach ($orders as $order) {
                         if ($order->total_amount <= 0) continue;

                         // পয়েন্ট ক্যালকুলেশন লজিক
                         if ($settings->earn_per_unit_amount > 0) {
                             $points = floor($order->total_amount / $settings->earn_per_unit_amount) * $settings->earn_points_per_unit;

                             if ($points > 0) {
                                 RewardPoint::create([
                                     'customer_id' => $order->customer_id,
                                     'order_id' => $order->id,
                                     'points' => $points,
                                     'type' => 'earned',
                                     'meta' => 'Historical points generated for Order #' . $order->invoice_no,
                                     'created_at' => $order->created_at, // অর্ডারের টাইমে লগ তৈরি হবে
                                 ]);
                                 
                                 $count++;
                                 $totalPoints += $points;
                             }
                         }
                     }
                 });
        });

        if ($count > 0) {
            return redirect()->back()->with('success', "Success! Generated $totalPoints points for $count past orders.");
        } else {
            return redirect()->back()->with('info', 'No new eligible orders found for the selected date range.');
        }

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error generating points: ' . $e->getMessage());
    }
}
}
