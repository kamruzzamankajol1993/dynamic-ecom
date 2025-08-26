<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    /**
     * Display the coupon management page.
     */
    public function index()
    {
        // Pass products and categories to the view for use in the modals.
        $products = Product::where('status', 1)->get(['id', 'name']);
        $categories = Category::where('status', 1)->get(['id', 'name']);
        return view('admin.coupon.index', compact('products', 'categories'));
    }

    /**
     * Fetch coupon data for the AJAX table.
     */
    public function data(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('code', 'LIKE', "%{$searchTerm}%");
        }

        $coupons = $query->latest()->paginate(10);
        return response()->json($coupons);
    }
public function show(Coupon $coupon)
{
    // Load the names of products and categories if they exist
    $products = collect();
    if ($coupon->product_ids) {
        $products = Product::whereIn('id', $coupon->product_ids)->pluck('name');
    }

    $categories = collect();
    if ($coupon->category_ids) {
        $categories = Category::whereIn('id', $coupon->category_ids)->pluck('name');
    }

    return view('admin.coupon.show', compact('coupon', 'products', 'categories'));
}
    /**
     * Store a newly created coupon via AJAX.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $coupon = Coupon::create($request->all());
        return response()->json(['success' => 'Coupon created successfully.', 'coupon' => $coupon]);
    }

    /**
     * Fetch a single coupon's data for the edit modal.
     */
    public function edit(Coupon $coupon)
    {
        return response()->json($coupon);
    }

    /**
     * Update an existing coupon via AJAX.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $coupon->update($request->all());
        return response()->json(['success' => 'Coupon updated successfully.', 'coupon' => $coupon]);
    }

    /**
     * Delete a coupon via AJAX.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(['success' => 'Coupon deleted successfully.']);
    }
}
