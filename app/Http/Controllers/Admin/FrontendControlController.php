<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\BundleOffer; // Import the BundleOffer model
use App\Models\Setting;
use App\Models\ExtraCategory; 
use App\Models\Support;
class FrontendControlController extends Controller
{
    public function index()
    {
        // Sync both data sources with the menu items table
        $this->syncMenuItems();

        //dd($this->syncMenuItems());

        $menuItems = MenuItem::orderBy('order')->get();
        
        $settings = Setting::pluck('value', 'key');
        $headerColor = $settings['header_color'] ?? '#FFFFFF';
        $menuLimit = $settings['menu_limit'] ?? 8;
 $support = Support::first(); 
        return view('admin.frontend-control.index', compact('support','menuItems', 'headerColor', 'menuLimit'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'menus' => 'required|array',
             'menus.*.name' => 'required|string|max:255', 
            'menus.*.id' => 'required|exists:menu_items,id',
            'menus.*.route' => 'nullable|string',
            'menus.*.order' => 'required|integer',
            'header_color' => 'required|string',
            'menu_limit' => 'required|integer|min:1',
             'support_title' => 'nullable|string|max:255',
            'support_phone' => 'nullable|string|max:255',
        ]);

         if ($request->filled('support_title') && $request->filled('support_phone')) {
            Support::updateOrCreate(
                ['id' => 1], // Always target the record with ID=1 for simplicity
                [
                    'title' => $request->support_title,
                    'phone' => $request->support_phone
                ]
            );
        } else {
            // If fields are empty, delete the record to keep the table clean.
            Support::truncate();
        }

        // Save Menu Items
        foreach ($request->menus as $menuData) {
            MenuItem::where('id', $menuData['id'])->update([
                'name' => $menuData['name'], 
                'route' => $menuData['route'],
                'order' => $menuData['order'],
                'is_visible' => isset($menuData['is_visible']) ? 1 : 0,
            ]);
        }

        // Save Header Settings
        Setting::updateOrCreate(
            ['key' => 'header_color'],
            ['value' => $request->header_color]
        );

        Setting::updateOrCreate(
            ['key' => 'menu_limit'],
            ['value' => $request->menu_limit]
        );

        return redirect()->back()->with('success', 'Frontend settings updated successfully!');
    }

    public function destroyMenuItem(MenuItem $menuItem)
    {
        $menuItem->delete();
        //return response()->json(['message' => 'Menu item deleted successfully.']);
          return redirect()->back()->with('success', 'Menu item deleted successfully!');
    }

    private function syncMenuItems()
    {
        // --- MODIFIED: The sync logic now uses the permanent source_id ---

        // Sync Categories
        $categories = Category::where('status', 1)->get();
        foreach ($categories as $category) {
            MenuItem::firstOrCreate(
                ['source_id' => $category->id, 'type' => 'category'], // Check by ID and type
                [
                    'name' => $category->name, // Set name only on creation
                    'route' => '/category/' . $category->slug
                ]
            );
        }

        // Sync Bundle Offer Groups
        $bundleOffers = BundleOffer::all();
        foreach ($bundleOffers as $group) {
            MenuItem::firstOrCreate(
                ['source_id' => $group->id, 'type' => $group->title], // Check by ID and type
                [
                    'name' => $group->name, // Set name only on creation
                    'route' => '/offer/' . $group->slug
                ]
            );
        }
        
        // Sync Extra Categories
        $extraCategories = ExtraCategory::where('status', 1)->get();
        foreach ($extraCategories as $extraCategory) {
            MenuItem::firstOrCreate(
                ['source_id' => $extraCategory->id, 'type' => 'extracategory'], // Check by ID and type
                [
                    'name' => $extraCategory->name, // Set name only on creation
                    'route' => '/extra-category/' . $extraCategory->slug
                ]
            );
        }

        
    }
}
