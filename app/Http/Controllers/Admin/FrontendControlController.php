<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\BundleOffer; // Import the BundleOffer model
use App\Models\Setting;

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

        return view('admin.frontend-control.index', compact('menuItems', 'headerColor', 'menuLimit'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|exists:menu_items,id',
            'menus.*.route' => 'nullable|string',
            'menus.*.order' => 'required|integer',
            'header_color' => 'required|string',
            'menu_limit' => 'required|integer|min:1',
        ]);

        // Save Menu Items
        foreach ($request->menus as $menuData) {
            MenuItem::where('id', $menuData['id'])->update([
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

    private function syncMenuItems()
    {
        // Sync Categories
        $categories = Category::where('status', 1)->get();
        foreach ($categories as $category) {
            MenuItem::firstOrCreate(
                ['name' => $category->name, 'type' => 'category'],
                ['route' => '/category/' . $category->slug]
            );
        }

        // Sync Bundle Offer Groups
        $BundleOffers = BundleOffer::all();
        foreach ($BundleOffers as $group) {
            MenuItem::firstOrCreate(
                ['name' => $group->name, 'type' => $group->title],
                ['route' => '/offer/' . $group->slug] // Example route
            );
        }
    }
}
