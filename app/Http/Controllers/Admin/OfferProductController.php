<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfferProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferProductController extends Controller
{
    public function index(): View
    {
        return view('admin.offer_product.index');
    }

    public function create(): View
    {
        $products = Product::where('status', 1)->orderBy('name')->get();
        return view('admin.offer_product.create', compact('products'));
    }

    public function data(Request $request)
    {
        $query = OfferProduct::with('product');

        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', $request->search . '%');
            });
        }

        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $offerProducts = $query->paginate(10);

        return response()->json([
            'data' => $offerProducts->items(),
            'total' => $offerProducts->total(),
            'current_page' => $offerProducts->currentPage(),
            'last_page' => $offerProducts->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:offer_products,product_id',
            'discount_price' => 'required|numeric|min:0',
            'offer_start_date' => 'required|date',
            'offer_end_date' => 'required|date|after_or_equal:offer_start_date',
        ]);

        OfferProduct::create($request->all());

        return redirect()->route('offer-product.index')->with('success', 'Offer Product created successfully!');
    }

    public function edit(OfferProduct $offerProduct): View
    {
        $products = Product::where('status', 1)->orderBy('name')->get();
        return view('admin.offer_product.edit', compact('offerProduct', 'products'));
    }

    public function update(Request $request, OfferProduct $offerProduct)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:offer_products,product_id,' . $offerProduct->id,
            'offer_start_date' => 'required|date',
            'discount_price' => 'required|numeric|min:0',
            'offer_end_date' => 'required|date|after_or_equal:offer_start_date',
            'status' => 'required|boolean',
        ]);

        $offerProduct->update($request->all());

        return redirect()->route('offer-product.index')->with('success', 'Offer Product updated successfully!');
    }

    public function destroy(OfferProduct $offerProduct)
    {
        $offerProduct->delete();
        return response()->json(['message' => 'Offer Product deleted successfully']);
    }
}
