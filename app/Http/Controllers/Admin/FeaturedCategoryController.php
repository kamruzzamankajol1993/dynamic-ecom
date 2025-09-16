<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeaturedCategory;
use Illuminate\Validation\Rule;

class FeaturedCategoryController extends Controller
{
    /**
     * Display the settings page for featured sections.
     */
    public function index()
    {
        $options = [
            'trending' => 'Trending',
            'new' => 'New',
            'discount' => 'Discount',
        ];

        // Fetch the currently saved settings. The value will be a string or null.
        $settings = FeaturedCategory::pluck('value', 'key')->all();
        
        $firstRowSetting = $settings['first_row_category'] ?? null;
        $secondRowSetting = $settings['second_row_category'] ?? null;

        return view('admin.featured_category.index', compact('options', 'firstRowSetting', 'secondRowSetting'));
    }

    /**
     * Update the featured section settings in storage.
     */
    public function update(Request $request)
    {
        $allowedValues = ['trending', 'new', 'discount'];

        // Validate that inputs are nullable strings and exist in our allowed list.
        $request->validate([
            'first_row' => ['nullable', 'string', Rule::in($allowedValues)],
            'second_row' => ['nullable', 'string', Rule::in($allowedValues)],
        ]);
        
        // Manually check for exclusivity: if the first row has a value, it cannot
        // be the same as the second row's value.
        if ($request->filled('first_row') && $request->first_row === $request->second_row) {
            return back()->withErrors(['first_row' => 'The same type cannot be selected in both rows.'])->withInput();
        }

        // Save the setting for the first row
        FeaturedCategory::updateOrCreate(
            ['key' => 'first_row_category'], // Using a singular key now
            ['value' => $request->first_row]
        );

        // Save the setting for the second row
        FeaturedCategory::updateOrCreate(
            ['key' => 'second_row_category'], // Using a singular key now
            ['value' => $request->second_row]
        );

        return redirect()->route('featured-category.index')->with('success', 'Featured settings updated successfully.');
    }
}