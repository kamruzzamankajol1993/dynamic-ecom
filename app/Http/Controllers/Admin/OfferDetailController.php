<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BundleOffer;
use App\Models\BundleOfferProduct;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
class OfferDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     * This method now just returns the view. Data is fetched via AJAX.
     */
    public function index()
    {
        return view('admin.offerProduct.index');
    }

    /**
     * Fetch data for the index page table via AJAX.
     */
    public function data(Request $request)
    {
        $query = BundleOfferProduct::with('bundleOffer');

        // Handle search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('title', 'like',$searchTerm . '%')
                  ->orWhereHas('bundleOffer', function ($q) use ($searchTerm) {
                      $q->where('name', 'like',$searchTerm . '%');
                  });
        }

        // Handle sorting
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $offerProducts = $query->paginate(10);

        return response()->json($offerProducts);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bundleOffers = BundleOffer::where('status', 1)->pluck('name', 'id');
        return view('admin.offerProduct.create', compact('bundleOffers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bundle_offer_id' => 'required|exists:bundle_offers,id',
            'title' => 'required|string|max:255',
            'buy_quantity' => 'required|integer|min:1',
            'get_quantity' => 'nullable|integer|min:0',
            'product_id' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    $totalQuantity = (int)$request->buy_quantity + (int)$request->get_quantity;
                    if (count($value) > $totalQuantity) {
                        $fail('The number of selected products cannot exceed the sum of Buy and Get quantities.');
                    }
                },
            ],
            'product_id.*' => 'required|exists:products,id',
            'discount_price' => 'nullable|numeric|min:0',
        ]);

        BundleOfferProduct::create($request->all());

        return redirect()->route('offer-product.index')->with('success', 'Product Deal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BundleOfferProduct $offerProduct)
    {
        $productIds = $offerProduct->product_id ?? [];
        $products = Product::whereIn('id', $productIds)->get();
        return view('admin.offerProduct.show', compact('offerProduct', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BundleOfferProduct $offerProduct)
    {
        $bundleOffers = BundleOffer::where('status', 1)->pluck('name', 'id');
        $selectedProducts = Product::whereIn('id', $offerProduct->product_id)->get();
        return view('admin.offerProduct.edit', compact('offerProduct', 'bundleOffers', 'selectedProducts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BundleOfferProduct $offerProduct)
    {
                $request->validate([
            'bundle_offer_id' => 'required|exists:bundle_offers,id',
            'title' => 'required|string|max:255',
            'buy_quantity' => 'required|integer|min:1',
            'get_quantity' => 'nullable|integer|min:0',
            'product_id' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    $totalQuantity = (int)$request->buy_quantity + (int)$request->get_quantity;
                    if (count($value) > $totalQuantity) {
                        $fail('The number of selected products cannot exceed the sum of Buy and Get quantities.');
                    }
                },
            ],
            'product_id.*' => 'required|exists:products,id',
            'discount_price' => 'nullable|numeric|min:0',
        ]);

        $offerProduct->update($request->all());

        return redirect()->route('offer-product.index')->with('success', 'Product Deal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BundleOfferProduct $offerProduct)
    {
        $offerProduct->delete();
        // Since we will use AJAX for deletion, we return a JSON response
        return response()->json(['message' => 'Product Deal deleted successfully.']);
    }
}
