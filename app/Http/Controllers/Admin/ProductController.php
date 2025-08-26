<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use App\Models\Fabric;
use App\Models\Unit;
use App\Models\Color;
use App\Models\Size;
use App\Models\AnimationCategory; // Add this
use App\Models\SizeChart;
use App\Models\AssignChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;
use App\Models\ProductVariant;
use App\Models\AssignCategory;

class ProductController extends Controller
{
    private function getProductData()
    {
        return [
            'brands' => Brand::where('status', 1)->get(),
            'categories' => Category::where('status', 1)->get(),
            'fabrics' => Fabric::where('status', 1)->get(),
            'units' => Unit::where('status', 1)->get(),
            'colors' => Color::where('status', 1)->get(),
            'sizes' => Size::where('status', 1)->get(),
            'size_charts' => SizeChart::where('status', 1)->get(),
            'animation_categories' => AnimationCategory::where('status', 1)->get(),
        ];
    }

    // AJAX method to get subcategories
    public function getSubcategories($categoryId)
    {
        return response()->json(Subcategory::where('category_id', $categoryId)->where('status', 1)->get());
    }

    // AJAX method to get sub-subcategories
    public function getSubSubcategories($subcategoryId)
    {
        return response()->json(SubSubcategory::where('subcategory_id', $subcategoryId)->where('status', 1)->get());
    }

    // AJAX method to get size chart entries
    public function getSizeChartEntries($id)
    {
        return response()->json(SizeChart::with('entries')->findOrFail($id));
    }


     public function index()
    {

        
        // Pass both sizes (for the modal) and categories (for the new filter)
        $sizes = Size::all()->keyBy('id');
        $categories = Category::where('status', 1)->orderBy('name')->get(); // MODIFIED: Get categories for the filter
        return view('admin.product.index', compact('sizes', 'categories')); // MODIFIED: Pass categories to the view
    }

    public function data(Request $request)
    {
        $query = Product::with(['category', 'variants.color']);

        // --- NEW: Advanced Filtering Logic ---
        if ($request->filled('product_name')) {
            $query->where('name', 'like',$request->product_name . '%');
        }

        if ($request->filled('product_code')) {
            $query->where('product_code', 'like',$request->product_code . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        // --- END: Advanced Filtering Logic ---

        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $products = $query->paginate(10);

        return response()->json([
            'data' => $products->items(),
            'total' => $products->total(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ]);
    }


    public function create()
    {
        return view('admin.product.create', $this->getProductData());
    }

    public function store(Request $request)
    {

        ///dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'base_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'thumbnail_image.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'size_chart_id' => 'nullable|exists:size_charts,id',
            'chart_entries' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $thumbnailPaths = [];
            $mainPaths = [];
            if ($request->hasFile('thumbnail_image')) {
                foreach ($request->file('thumbnail_image') as $image) {
                    $thumbnailPaths[] = $this->uploadImageMobile($image, 'products/thumbnails');
                }

                 foreach ($request->file('thumbnail_image') as $image) {
                    $mainPaths[] = $this->uploadImage($image, 'products/thumbnails');
                }
            }

            $product = Product::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'product_code' => $request->product_code,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'sub_subcategory_id' => $request->sub_subcategory_id,
                'fabric_id' => $request->fabric_id,
                'unit_id' => $request->unit_id,
                'description' => $request->description,
                'base_price' => $request->base_price,
                'purchase_price' => $request->purchase_price,
                'discount_price' => $request->discount_price,
                'thumbnail_image' => $thumbnailPaths,
                'main_image' => $mainPaths,
                'status' => $request->status ?? 1,
            ]);

              // Handle Assigned Categories
            if ($request->has('animation_category_ids')) {
                foreach ($request->animation_category_ids as $id) {
                    $category = AnimationCategory::find($id);
                    if ($category) {
                        $product->assigns()->create([
                            'category_id' => $id,
                            'category_name' => $category->name,
                            'type' => 'animation'
                        ]);
                    }
                }
            }
            if ($request->has('other_categories')) {
                foreach ($request->other_categories as $name) {
                    $product->assigns()->create([
                        'category_name' => $name,
                        'type' => 'other'
                    ]);
                }
            }

            // Handle Assign Chart
            if ($request->filled('size_chart_id') && $request->has('chart_entries')) {
                $assignChart = $product->assignChart()->create([
                    'size_chart_id' => $request->size_chart_id,
                ]);
                foreach ($request->chart_entries as $entry) {
                    $assignChart->entries()->create($entry);
                }
            }

            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    $variantImagePath = null;
                    $variantImagePathmain = null;
                    if (isset($variantData['image'])) {
                        $variantImagePath = $this->uploadImageMobile($variantData['image'], 'products/variants');
                        $variantImagePathmain = $this->uploadImage($variantData['image'], 'products/variants');
                    }

                    // Filter out sizes that don't have a quantity
                   // **FIXED LOGIC HERE**
                    $sizesWithKeys = array_filter($variantData['sizes'], fn($size) => isset($size['quantity']) && $size['quantity'] !== null);
                    $sizes = array_values($sizesWithKeys); // Re-index the array to remove keys

                    if (!empty($sizes)) {
                        $product->variants()->create([
                            'color_id' => $variantData['color_id'],
                                                        'variant_sku' => $variantData['variant_sku'],

                            'variant_image' => $variantImagePath,
                            'main_image' => $variantImagePathmain,
                            'sizes' => $sizes,
                            'additional_price' => $variantData['additional_price'] ?? 0,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('product.index')->with('success', 'Product created successfully.');
    }

      public function show(Product $product)
    {
        // Eager load all necessary relationships for the view
        $product->load([
            'brand',
            'category',
            'subcategory',
            'subSubcategory',
            'fabric',
            'unit',
            'variants.color',
            'assignChart.entries',
            'assignChart.originalSizeChart' // Load the original chart for its name
        ]);
        return view('admin.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $data = $this->getProductData();
        $data['product'] = $product->load('variants.color', 'assignChart.entries');
        return view('admin.product.edit', $data);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'nullable|string|unique:products,product_code,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'base_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|lt:base_price',
            'thumbnail_image.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variants' => 'nullable|array',
            'delete_images' => 'nullable|array',
        ]);

        //dd($request->all());

        DB::transaction(function () use ($request, $product) {

         

           // --- REVISED IMAGE HANDLING LOGIC ---

            // Start with the images that are already saved in the database.
            $existingThumbnails = $product->thumbnail_image ?? [];
            $existingMains = $product->main_image ?? [];

            // 1. Handle deletion of existing images
            if ($request->has('delete_images')) {
                $imagesToDelete = $request->input('delete_images');
                $indicesToDelete = [];

                // Find the index of each image marked for deletion
                foreach ($imagesToDelete as $pathToDelete) {
                    $index = array_search($pathToDelete, $existingThumbnails);
                    if ($index !== false) {
                        $indicesToDelete[] = $index;
                    }
                }

                // Delete the files from the server
                if (!empty($indicesToDelete)) {
                    foreach ($indicesToDelete as $index) {
                        // Delete both the thumbnail and the corresponding main image file
                        if (isset($existingThumbnails[$index])) {
                            $this->deleteImage($existingThumbnails[$index]);
                        }
                        if (isset($existingMains[$index])) {
                            $this->deleteImage($existingMains[$index]);
                        }
                        // Unset the entry from the arrays
                        unset($existingThumbnails[$index]);
                        unset($existingMains[$index]);
                    }
                }
            }

            // Re-index the arrays to prevent issues after unsetting elements
            $finalThumbnails = array_values($existingThumbnails);
            $finalMains = array_values($existingMains);

            // 2. Handle the upload of new images
            if ($request->hasFile('thumbnail_image')) {
                foreach ($request->file('thumbnail_image') as $image) {
                    // Upload and add the new paths to our final arrays
                    $finalThumbnails[] = $this->uploadImageMobile($image, 'products/thumbnails');
                    $finalMains[] = $this->uploadImage($image, 'products/thumbnails');
                }
            }
            
            // --- END OF REVISED IMAGE HANDLING LOGIC ---

            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'product_code' => $request->product_code,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'sub_subcategory_id' => $request->sub_subcategory_id,
                'fabric_id' => $request->fabric_id,
                'unit_id' => $request->unit_id,
                'description' => $request->description,
                'base_price' => $request->base_price,
                'purchase_price' => $request->purchase_price,
                'discount_price' => $request->discount_price,
                'thumbnail_image' => $finalThumbnails, // Save the updated array of thumbnails
                'main_image' => $finalMains, 
                'status' => $request->status ?? 1,
            ]);

               $product->assigns()->delete();
            if ($request->has('animation_category_ids')) {
                foreach ($request->animation_category_ids as $id) {
                    $category = AnimationCategory::find($id);
                    if ($category) {
                        $product->assigns()->create([
                            'category_id' => $id,
                            'category_name' => $category->name,
                            'type' => 'animation'
                        ]);
                    }
                }
            }
            if ($request->has('other_categories')) {
                foreach ($request->other_categories as $name) {
                    $product->assigns()->create([
                        'category_name' => $name,
                        'type' => 'other'
                    ]);
                }
            }

             // Handle Assign Chart update (delete old, create new)
            if ($product->assignChart) {
                $product->assignChart->entries()->delete();
                $product->assignChart()->delete();
            }
            if ($request->filled('size_chart_id') && $request->has('chart_entries')) {
                $assignChart = $product->assignChart()->create([
                    'size_chart_id' => $request->size_chart_id,
                ]);
                foreach ($request->chart_entries as $entry) {
                    $assignChart->entries()->create($entry);
                }
            }

            // Delete old variants and their images before creating new ones
            
            $product->variants()->delete();

            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    $variantImagePath = null;
                    $variantImagePathmain = null;
                    if (isset($variantData['image'])) {

                        foreach ($product->variants as $variant) {
                $this->deleteImage($variant->variant_image);
            }
                        // This assumes you are using a file input with the name 'variants[index][image]'
                        $variantImagePath = $this->uploadImageMobile($variantData['image'], 'products/variants');
                        $variantImagePathmain = $this->uploadImage($variantData['image'], 'products/variants');
                    } elseif (isset($variantData['existing_image'])) {
                        // This handles cases where the image is not being changed
                        $variantImagePath = $variantData['existing_image'];
                        $variantImagePathmain = $variantData['existing_image'];
                    }

                    $sizesWithKeys = array_filter($variantData['sizes'], fn($size) => isset($size['quantity']) && $size['quantity'] !== null);
                    $sizes = array_values($sizesWithKeys); // Re-index the array

                    if (!empty($sizes)) {
                        $product->variants()->create([
                            'color_id' => $variantData['color_id'],
                             'variant_sku' => $variantData['variant_sku'],
                            'variant_image' => $variantImagePath,
                            'main_image' => $variantImagePathmain,
                            'sizes' => $sizes,
                            'additional_price' => $variantData['additional_price'] ?? 0,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('product.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            foreach ($product->variants as $variant) {
                $this->deleteImage($variant->variant_image);
            }
            $this->deleteImage($product->thumbnail_image);
            $this->deleteImage($product->main_image);
            $product->delete();
        });

        return response()->json(['message' => 'Product deleted successfully.']);
    }

    private function uploadImage($image, $directory)
    {
        $imageName = Str::uuid() . '.' . 'webp';
        $destinationPath = public_path('uploads/' . $directory);
        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }
        Image::read($image->getRealPath())->resize(600, 600, function ($c) {
            $c->aspectRatio(); $c->upsize();
        })->save($destinationPath . '/' . $imageName);
        return $directory . '/' . $imageName;
    }

    private function uploadImageMobile($image, $directory)
    {
        $imageName = Str::uuid() . '.' . 'webp';
        $destinationPath = public_path('uploads/' . $directory);
        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }
        Image::read($image->getRealPath())->resize(300, 300, function ($c) {
            $c->aspectRatio(); $c->upsize();
        })->save($destinationPath . '/' . $imageName);
        return $directory . '/' . $imageName;
    }

    private function deleteImage($paths)
    {
        if (is_array($paths)) {
            foreach ($paths as $path) {
                if ($path && File::exists(public_path('uploads/' . $path))) {
                    File::delete(public_path('uploads/' . $path));
                }
            }
        } elseif (is_string($paths)) {
            if ($paths && File::exists(public_path('uploads/' . $paths))) {
                File::delete(public_path('uploads/' . $paths));
            }
        }
    }
}
