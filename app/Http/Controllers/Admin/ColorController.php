<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColorController extends Controller
{
    public function index(): View
    {
        return view('admin.color.index');
    }

    public function data(Request $request)
    {
        $query = Color::query();

        if ($request->filled('search')) {
            $query->where('name', 'like',$request->search . '%')
                  ->orWhere('code', 'like',$request->search . '%');
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $colors = $query->paginate(10);

        return response()->json([
            'data' => $colors->items(),
            'total' => $colors->total(),
            'current_page' => $colors->currentPage(),
            'last_page' => $colors->lastPage(),
        ]);
    }

    public function show($id)
    {
        return response()->json(Color::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:colors,name',
            'code' => 'nullable|string|max:255',
        ]);

        Color::create($request->all());
        return redirect()->back()->with('success', 'Color created successfully!');
    }

    public function update(Request $request, $id)
    {
        $color = Color::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:colors,name,' . $id,
            'code' => 'nullable|string|max:255',
        ]);

        $color->update($request->all());
        return response()->json(['message' => 'Color updated successfully']);
    }

    public function destroy($id)
    {
        Color::findOrFail($id)->delete();
        return response()->json(['message' => 'Color deleted successfully']);
    }
}
