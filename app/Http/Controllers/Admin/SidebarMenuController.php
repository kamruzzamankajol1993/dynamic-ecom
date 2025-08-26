<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SidebarMenu;
use App\Models\Category;

class SidebarMenuController extends Controller
{
    public function index()
    {
        $this->syncSidebarMenuItems();
        $menuItems = SidebarMenu::orderBy('order')->get();
        return view('admin.sidebar-menu.index', compact('menuItems'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|exists:sidebar_menus,id',
            'menus.*.route' => 'nullable|string',
            'menus.*.order' => 'required|integer',
        ]);

        foreach ($request->menus as $menuData) {
            SidebarMenu::where('id', $menuData['id'])->update([
                'route' => $menuData['route'],
                'order' => $menuData['order'],
                'is_visible' => isset($menuData['is_visible']) ? 1 : 0,
            ]);
        }

        return redirect()->back()->with('success', 'Sidebar menu updated successfully!');
    }

    private function syncSidebarMenuItems()
    {
        $categories = Category::where('status', 1)->get();
        foreach ($categories as $category) {
            SidebarMenu::firstOrCreate(
                ['name' => $category->name],
                ['route' => '/category/' . $category->slug]
            );
        }
    }
}