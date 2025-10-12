<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaWisePrice;
use Illuminate\Http\Request;

class AreaWisePriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // The view is now just a shell, data will be loaded via AJAX
        return view('admin.area_wise_price.index');
    }

    /**
     * Fetch data for the table via AJAX.
     */
    public function data(Request $request)
    {
        $query = AreaWisePrice::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }

        $prices = $query->paginate(10); // Using 10 to match product module

        return response()->json([
            'data' => $prices->items(),
            'total' => $prices->total(),
            'current_page' => $prices->currentPage(),
            'last_page' => $prices->lastPage(),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.area_wise_price.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'label' => 'required|string|max:255',
            'days' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        AreaWisePrice::create($validatedData);

        return redirect()->route('area-wise-price.index')->with('success', 'Area wise price created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AreaWisePrice $areaWisePrice)
    {
        return redirect()->route('area-wise-price.edit', $areaWisePrice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AreaWisePrice $areaWisePrice)
    {
        return view('admin.area_wise_price.edit', ['price' => $areaWisePrice]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AreaWisePrice $areaWisePrice)
    {
        $validatedData = $request->validate([
            'label' => 'required|string|max:255',
            'days' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $areaWisePrice->update($validatedData);

        return redirect()->route('area-wise-price.index')->with('success', 'Area wise price updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AreaWisePrice $areaWisePrice)
    {
        // UPDATED: Now returns a JSON response for AJAX calls
        try {
            $areaWisePrice->delete();
            return response()->json(['message' => 'Entry deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not delete the entry.'], 500);
        }
    }
}