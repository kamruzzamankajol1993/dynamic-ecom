<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BundleOffer;
use App\Models\BundleOfferProduct;
use App\Models\Product;
use App\Models\Category;
use App\Models\AssignCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
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

    //dd($request->all());
        // MODIFIED: Updated validation and logic
       // MODIFIED: Added custom validation rule for discount_price
        $request->validate([
            'bundle_offer_id' => 'required|exists:bundle_offers,id',
            'title' => 'required|string|max:255',
            'buy_quantity' => 'required|integer|min:1',
            'get_quantity' => 'nullable|integer|min:0',
            'product_id' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    $buyQuantity = (int) $request->input('buy_quantity');
                    if (!empty($value) && count($value) < $buyQuantity) {
                        $fail("The number of selected products must be equal to or greater than the Buy Quantity ({$buyQuantity}). You have selected " . count($value) . ".");
                    }
                }
            ],
            'product_id.*' => 'exists:products,id',
            'category_id' => 'nullable|array',
            'category_id.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'discount_price' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $buyQuantity = (int) $request->input('buy_quantity');
                    $productIds = $request->input('product_id', []);

                    if ($buyQuantity > 0 && !empty($productIds) && count($productIds) >= $buyQuantity) {
                        $productIdsToSum = array_slice($productIds, 0, $buyQuantity);
                        $totalOriginalPrice = Product::whereIn('id', $productIdsToSum)->sum('base_price');

                        if ($value > $totalOriginalPrice) {
                            $fail("The discount price (৳" . number_format($value) . ") cannot be greater than the total original price of the 'buy' products (৳" . number_format($totalOriginalPrice) . ").");
                        }
                    }
                }
            ],
        ]);

        if (empty($request->product_id) && empty($request->category_id)) {
            return back()->withErrors(['selection' => 'You must select at least one product or one category.'])->withInput();
        }

        $data = $request->all();
        if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = 'bundle_' . time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('uploads/bundle_products');

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        Image::read($image->getRealPath())->save($destinationPath . '/' . $imageName);
        $data['image'] = 'public/uploads/bundle_products/' . $imageName;
    }
        $data['product_id'] = $request->product_id ?? [];
        $data['category_id'] = $request->category_id ?? [];
$data['is_custom'] = $request->has('is_custom') ? 1 : 0;
        BundleOfferProduct::create($data);

        return redirect()->route('offer-product.index')->with('success', 'Product Deal created successfully.');
    }
public function bulkCustomUpdate(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'status' => 'required|boolean',
    ]);

    \App\Models\BundleOfferProduct::whereIn('id', $request->ids)->update(['is_custom' => $request->status]);

    return response()->json(['message' => 'Deal customization status updated successfully.']);
}
    /**
     * Display the specified resource.
     */
  public function show(BundleOfferProduct $offerProduct)
    {
        // Get the individually selected product IDs from the deal
        $productIds = $offerProduct->product_id ?? [];
        
        // Get the selected category IDs from the deal
        $categoryIds = $offerProduct->category_id ?? [];

        $productsFromCategories = [];
        if (!empty($categoryIds)) {
            // UPDATED: Fetch product IDs from the 'AssignCategory' pivot table
            // based on the selected categories.
            $productsFromCategories = \App\Models\AssignCategory::whereIn('category_id', $categoryIds)
                                                                ->pluck('product_id')
                                                                ->toArray();
        }

        // Merge the product IDs from direct selection and from categories, removing duplicates
        $allProductIds = array_unique(array_merge($productIds, $productsFromCategories));
        
        // Fetch the full product models for all collected IDs
        $products = \App\Models\Product::whereIn('id', $allProductIds)->get();
        
        // Fetch the category models for display purposes
        $categories = \App\Models\Category::whereIn('id', $categoryIds)->get();

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

   // dd($request->all());
        $request->validate([
            'bundle_offer_id' => 'required|exists:bundle_offers,id',
            'title' => 'required|string|max:255',
            'buy_quantity' => 'required|integer|min:1',
            'get_quantity' => 'nullable|integer|min:0',
            'product_id' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    $buyQuantity = (int) $request->input('buy_quantity');
                    if (!empty($value) && count($value) < $buyQuantity) {
                        $fail("The number of selected products must be equal to or greater than the Buy Quantity ({$buyQuantity}). You have selected " . count($value) . ".");
                    }
                }
            ],
            'product_id.*' => 'exists:products,id',
            'category_id' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'category_id.*' => 'exists:categories,id',
            'discount_price' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $buyQuantity = (int) $request->input('buy_quantity');
                    $productIds = $request->input('product_id', []);

                    if ($buyQuantity > 0 && !empty($productIds) && count($productIds) >= $buyQuantity) {
                        $productIdsToSum = array_slice($productIds, 0, $buyQuantity);
                        $totalOriginalPrice = Product::whereIn('id', $productIdsToSum)->sum('base_price');

                        if ($value > $totalOriginalPrice) {
                            $fail("The discount price (৳" . number_format($value) . ") cannot be greater than the total original price of the 'buy' products (৳" . number_format($totalOriginalPrice) . ").");
                        }
                    }
                }
            ],
        ]);

        if (empty($request->product_id) && empty($request->category_id)) {
            return back()->withErrors(['selection' => 'You must select at least one product or one category.'])->withInput();
        }

        $data = $request->all();
        if ($request->hasFile('image')) {
        // Delete old image
        if ($offerProduct->image && File::exists(public_path($offerProduct->image))) {
            File::delete(public_path($offerProduct->image));
        }

        $image = $request->file('image');
        $imageName = 'bundle_' . time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('uploads/bundle_products');
        
        Image::read($image->getRealPath())->save($destinationPath . '/' . $imageName);
        $data['image'] = 'public/uploads/bundle_products/' . $imageName;
    }
        $data['product_id'] = $request->product_id ?? [];
        $data['category_id'] = $request->category_id ?? [];
$data['is_custom'] = $request->has('is_custom') ? 1 : 0;
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

        $getAllid = AssignCategory::whereIn('category_id', $request->category_ids)
        ->pluck('product_id');

        $products = Product::whereIn('id',$getAllid)->where('status', 1)->get();

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
