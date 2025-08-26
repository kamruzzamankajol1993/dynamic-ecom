<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fabric;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FabricController extends Controller
{
    public function index(): View
    {
        return view('admin.fabric.index');
    }

    public function data(Request $request)
    {
        $query = Fabric::query();

        if ($request->filled('search')) {
            $query->where('name', 'like',$request->search . '%')
                  ->orWhere('code', 'like',$request->search . '%');
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $fabrics = $query->paginate(10);

        return response()->json([
            'data' => $fabrics->items(),
            'total' => $fabrics->total(),
            'current_page' => $fabrics->currentPage(),
            'last_page' => $fabrics->lastPage(),
        ]);
    }

    public function show($id)
    {
        return response()->json(Fabric::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:fabrics,name',
            'code' => 'nullable|string|max:255',
        ]);

        Fabric::create($request->all());
        return redirect()->back()->with('success', 'Fabric created successfully!');
    }

    public function update(Request $request, $id)
    {
        $fabric = Fabric::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:fabrics,name,' . $id,
            'code' => 'nullable|string|max:255',
        ]);

        $fabric->update($request->all());
        return response()->json(['message' => 'Fabric updated successfully']);
    }

    public function destroy($id)
    {
        Fabric::findOrFail($id)->delete();
        return response()->json(['message' => 'Fabric deleted successfully']);
    }
}
