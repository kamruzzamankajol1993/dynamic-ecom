<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeaturedCategory;
use Illuminate\Validation\Rule;

class FeaturedCategoryController extends Controller
{
    public function index()
    {
        $options = [
            'trending' => 'Trending',
            'new' => 'New',
            'discount' => 'Discount',
        ];

        $settings = FeaturedCategory::pluck('value', 'key')->all();
        
        $firstRowSetting = $settings['first_row_category'] ?? null;
        $secondRowSetting = $settings['second_row_category'] ?? null;

        // Fetch status for each row, defaulting to 'true' (visible) if not set
        $firstRowStatus = $settings['first_row_status'] ?? true;
        $secondRowStatus = $settings['second_row_status'] ?? true;

        return view('admin.featured_category.index', compact('options', 'firstRowSetting', 'secondRowSetting', 'firstRowStatus', 'secondRowStatus'));
    }

    public function update(Request $request)
    {
        $allowedValues = ['trending', 'new', 'discount'];

        $request->validate([
            'first_row' => ['nullable', 'string', Rule::in($allowedValues), 'different:second_row'],
            'second_row' => ['nullable', 'string', Rule::in($allowedValues)],
            'first_row_status' => 'nullable|boolean', // Added status validation
            'second_row_status' => 'nullable|boolean', // Added status validation
        ],[
            'first_row.different' => 'The same type cannot be selected in both rows.'
        ]);

        // Save category type for Row 1
        FeaturedCategory::updateOrCreate(
            ['key' => 'first_row_category'],
            ['value' => $request->first_row]
        );

        // Save status for Row 1
        FeaturedCategory::updateOrCreate(
            ['key' => 'first_row_status'],
            ['value' => $request->has('first_row_status') ? 1 : 0]
        );

        // Save category type for Row 2
        FeaturedCategory::updateOrCreate(
            ['key' => 'second_row_category'],
            ['value' => $request->second_row]
        );

        // Save status for Row 2
        FeaturedCategory::updateOrCreate(
            ['key' => 'second_row_status'],
            ['value' => $request->has('second_row_status') ? 1 : 0]
        );

        return redirect()->route('featured-category.index')->with('success', 'Featured settings updated successfully.');
    }
}