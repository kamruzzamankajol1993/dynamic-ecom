<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SizeChart;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SizeChartController extends Controller
{
    public function index(): View
    {
        return view('admin.size-chart.index');
    }

    public function data(Request $request)
    {
        $query = SizeChart::with('entries');

        if ($request->filled('search')) {
            $query->where('name', 'like',$request->search . '%');
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $charts = $query->paginate(10);

        return response()->json([
            'data' => $charts->items(),
            'total' => $charts->total(),
            'current_page' => $charts->currentPage(),
            'last_page' => $charts->lastPage(),
        ]);
    }

    public function show($id)
    {
        return response()->json(SizeChart::with('entries')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:size_charts,name',
            'entries' => 'required|array|min:1',
            'entries.*.size' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $sizeChart = SizeChart::create(['name' => $request->name]);

            foreach ($request->entries as $entryData) {
                $sizeChart->entries()->create($entryData);
            }
        });

        return redirect()->back()->with('success', 'Size Chart created successfully!');
    }

    public function update(Request $request, $id)
    {
        $sizeChart = SizeChart::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:size_charts,name,' . $id,
            'entries' => 'required|array|min:1',
            'entries.*.size' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $sizeChart) {
            $sizeChart->update([
                'name' => $request->name,
                'status' => $request->status
            ]);

            // Delete old entries and create new ones
            $sizeChart->entries()->delete();
            foreach ($request->entries as $entryData) {
                $sizeChart->entries()->create($entryData);
            }
        });

        return response()->json(['message' => 'Size Chart updated successfully']);
    }

    public function destroy($id)
    {
        // The 'cascade' onDelete in the migration will handle deleting entries
        SizeChart::findOrFail($id)->delete();
        return response()->json(['message' => 'Size Chart deleted successfully']);
    }
}
