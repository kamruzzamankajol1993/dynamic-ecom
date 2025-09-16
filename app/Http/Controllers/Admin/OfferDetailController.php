<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BundleOffer;
use App\Models\BundleOfferProduct;
use App\Models\Product;
use App\Models\Category;
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
        $categories = Category::where('status', 1)->pluck('name', 'id');
        $bundleOffers = BundleOffer::where('status', 1)->pluck('name', 'id');
        return view('admin.offerProduct.create', compact('bundleOffers', 'categories'));
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // MODIFIED: Updated validation and logic
        $request->validate([
            'bundle_offer_id' => 'required|exists:bundle_offers,id',
            'title' => 'required|string|max:255',
            'buy_quantity' => 'required|integer|min:1',
            'get_quantity' => 'nullable|integer|min:0',
            'product_id' => 'nullable|array',
            'product_id.*' => 'exists:products,id',
            'category_id' => 'nullable|array',
            'category_id.*' => 'exists:categories,id',
            'discount_price' => 'nullable|numeric|min:0',
        ]);

        if (empty($request->product_id) && empty($request->category_id)) {
            return back()->withErrors(['selection' => 'You must select at least one product or one category.'])->withInput();
        }

        $data = $request->all();
        $data['product_id'] = $request->product_id ?? [];
        $data['category_id'] = $request->category_id ?? [];

        BundleOfferProduct::create($data);

        return redirect()->route('offer-product.index')->with('success', 'Product Deal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BundleOfferProduct $offerProduct)
    {
        // MODIFIED: Logic to get products from both individual and category selections
        $productIds = $offerProduct->product_id ?? [];
        $categoryIds = $offerProduct->category_id ?? [];

        $productsFromCategories = [];
        if (!empty($categoryIds)) {
            $productsFromCategories = Product::whereIn('category_id', $categoryIds)->pluck('id')->toArray();
        }

        $allProductIds = array_unique(array_merge($productIds, $productsFromCategories));
        $products = Product::whereIn('id', $allProductIds)->get();
        $categories = Category::whereIn('id', $categoryIds)->get();

        return view('admin.offerProduct.show', compact('offerProduct', 'products', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BundleOfferProduct $offerProduct)
    {
        // MODIFIED: Fetch categories and selected category IDs
        $bundleOffers = BundleOffer::where('status', 1)->pluck('name', 'id');
        $categories = Category::where('status', 1)->pluck('name', 'id');
        $selectedProducts = Product::whereIn('id', $offerProduct->product_id ?? [])->get();
        $selectedCategoryIds = $offerProduct->category_id ?? [];

        return view('admin.offerProduct.edit', compact('offerProduct', 'bundleOffers', 'selectedProducts', 'categories', 'selectedCategoryIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BundleOfferProduct $offerProduct)
    {
        // MODIFIED: Updated validation and logic
        $request->validate([
            'bundle_offer_id' => 'required|exists:bundle_offers,id',
            'title' => 'required|string|max:255',
            'buy_quantity' => 'required|integer|min:1',
            'get_quantity' => 'nullable|integer|min:0',
            'product_id' => 'nullable|array',
            'product_id.*' => 'exists:products,id',
            'category_id' => 'nullable|array',
            'category_id.*' => 'exists:categories,id',
            'discount_price' => 'nullable|numeric|min:0',
        ]);

        if (empty($request->product_id) && empty($request->category_id)) {
            return back()->withErrors(['selection' => 'You must select at least one product or one category.'])->withInput();
        }

        $data = $request->all();
        $data['product_id'] = $request->product_id ?? [];
        $data['category_id'] = $request->category_id ?? [];

        $offerProduct->update($data);

        return redirect()->route('offer-product.index')->with('success', 'Product Deal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BundleOfferProduct $offerProduct)
    {
        $offerProduct->delete();
        return redirect()->route('offer-product.index')->with('success', 'Product Deal deleted successfully.');
        // Since we will use AJAX for deletion, we return a JSON response
       // return response()->json(['message' => 'Product Deal deleted successfully.']);
    }

       public function getProductsByCategories(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'integer|exists:categories,id',
        ]);

        $products = Product::whereIn('category_id', $request->category_ids)->get();

        // Format the response for Select2
        $response = $products->map(function($product) {
            return [
                'id' => $product->id,
                'text' => $product->name . ' (' . $product->product_code . ')'
            ];
        });

        return response()->json($response);
    }
}
