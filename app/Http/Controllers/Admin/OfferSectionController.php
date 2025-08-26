<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BundleOffer;
use App\Models\OffersectionSetting; // Use the new model

class OfferSectionController extends Controller
{
    public function index()
    {
        // Fetch all available bundle offers to populate the dropdown
        $bundleOffers = BundleOffer::where('status', 1)->get();

        // Get the first settings record, or create it with default values if it doesn't exist
        $settings = OffersectionSetting::firstOrCreate([], [
            'is_visible' => true,
            'background_color' => '#F8F9FA',
            'route' => '#'
        ]);

        return view('admin.offer-section-control.index', compact('bundleOffers', 'settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'background_color' => 'required|string',
            'bundle_offer_id' => 'nullable|exists:bundle_offers,id',
            'route' => 'nullable|string',
        ]);

        // Find the first (and only) settings record, or create it if it somehow got deleted
        $settings = OffersectionSetting::firstOrCreate([]);

        // Update the settings with the new values
        $settings->update([
            'is_visible' => $request->has('is_visible'),
            'background_color' => $request->background_color,
            'bundle_offer_id' => $request->bundle_offer_id,
            'route' => $request->route,
        ]);

        return redirect()->back()->with('success', 'Offer section settings updated successfully!');
    }
}
