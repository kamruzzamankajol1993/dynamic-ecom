<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SizeController extends Controller
{
    public function index(): View
    {
        return view('admin.size.index');
    }

    public function data(Request $request)
    {
        $query = Size::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('size_type', 'like', '%' . $request->search . '%');
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $sizes = $query->paginate(10);

        return response()->json([
            'data' => $sizes->items(),
            'total' => $sizes->total(),
            'current_page' => $sizes->currentPage(),
            'last_page' => $sizes->lastPage(),
        ]);
    }

    public function show($id)
    {
        return response()->json(Size::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:sizes,name',
            'code' => 'nullable|string|max:255',
            'size_type' => 'nullable|string|max:255',
        ]);

        Size::create($request->all());
        return redirect()->back()->with('success', 'Size created successfully!');
    }

    public function update(Request $request, $id)
    {
        $size = Size::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:sizes,name,' . $id,
            'code' => 'nullable|string|max:255',
            'size_type' => 'nullable|string|max:255',
        ]);

        $size->update($request->all());
        return response()->json(['message' => 'Size updated successfully']);
    }

    public function destroy($id)
    {
        Size::findOrFail($id)->delete();
        return response()->json(['message' => 'Size deleted successfully']);
    }
}
