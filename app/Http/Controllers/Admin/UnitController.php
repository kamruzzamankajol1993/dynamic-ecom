<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        return view('admin.unit.index');
    }

    public function data(Request $request)
    {
        $query = Unit::query();

        if ($request->filled('search')) {
            $query->where('name', 'like',$request->search . '%')
                  ->orWhere('code', 'like',$request->search . '%');
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $units = $query->paginate(10);

        return response()->json([
            'data' => $units->items(),
            'total' => $units->total(),
            'current_page' => $units->currentPage(),
            'last_page' => $units->lastPage(),
        ]);
    }

    public function show($id)
    {
        return response()->json(Unit::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:units,name',
            'code' => 'nullable|string|max:255',
        ]);

        Unit::create($request->all());
        return redirect()->back()->with('success', 'Unit created successfully!');
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:units,name,' . $id,
            'code' => 'nullable|string|max:255',
        ]);

        $unit->update($request->all());
        return response()->json(['message' => 'Unit updated successfully']);
    }

    public function destroy($id)
    {
        Unit::findOrFail($id)->delete();
        return response()->json(['message' => 'Unit deleted successfully']);
    }
}
