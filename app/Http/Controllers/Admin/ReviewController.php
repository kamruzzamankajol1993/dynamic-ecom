<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\ProductReviewImage; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Added for Transactions
use Illuminate\Support\Facades\Log; // Added for Logging errors
use Intervention\Image\Laravel\Facades\Image;

class ReviewController extends Controller
{
    public function index()
    {
        return view('admin.reviews.index');
    }

    public function data(Request $request)
    {
        $query = ProductReview::with(['product:id,name', 'user:id,name']);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('product', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('user', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $reviews = $query->paginate(10);

        return response()->json([
            'data' => $reviews->items(),
            'total' => $reviews->total(),
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
        ]);
    }

    public function show(ProductReview $review)
    {
        $review->load(['product', 'user', 'images']);
        return view('admin.reviews.show', compact('review'));
    }

    public function edit(ProductReview $review)
    {
        $review->load(['product', 'user']);
        return view('admin.reviews.edit', compact('review'));
    }

    public function create()
    {
        $products = Product::where('status', 1)->select('id', 'name')->get();
        // Fetch users (user_type 1 usually denotes admin/staff, adjust if needed for customers)
        $users = User::select('id', 'name', 'email')->where('user_type', 1)->get();

        return view('admin.reviews.create', compact('products', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_id'    => 'required|exists:users,id',
            'rating'     => 'required|integer|min:1|max:5',
            'description'=> 'nullable|string',
            'is_approved'=> 'required|boolean',
            'images'     => 'nullable|array',
            'images.*'   => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        DB::beginTransaction(); // Start Transaction

        try {
            $review = ProductReview::create([
                'product_id'  => $request->product_id,
                'user_id'     => $request->user_id,
                'rating'      => $request->rating,
                'description' => $request->description,
                'is_approved' => $request->is_approved,
                'is_upload_from_admin' => 1,
            ]);

            if ($request->hasFile('images')) {
                $destinationPath = public_path('review_images');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                foreach ($request->file('images') as $file) {
                    $imageName = 'review-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    $image = Image::read($file)->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                    $image->save($destinationPath . '/' . $imageName);
                    
                    $review->images()->create([
                        'image_path' => 'review_images/' . $imageName,
                        'is_upload_from_admin' => 1 
                    ]);
                }
            }

            DB::commit(); // Commit Transaction if everything is fine
            return redirect()->route('review.index')->with('success', 'Review created successfully.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback DB changes if error occurs
            Log::error('Review Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, ProductReview $review)
    {
        $request->validate([
            'description' => 'nullable|string',
            'is_approved' => 'required|boolean',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $review->update([
                'description' => $request->description,
                'is_approved' => $request->is_approved,
            ]);

            if ($request->hasFile('images')) {
                $destinationPath = public_path('review_images');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                foreach ($request->file('images') as $file) {
                    $imageName = 'review-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    $image = Image::read($file)->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                    $image->save($destinationPath . '/' . $imageName);
                    
                    $review->images()->create([
                        'image_path' => 'review_images/' . $imageName,
                        'is_upload_from_admin' => 1
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('review.index')->with('success', 'Review updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Review Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(ProductReview $review)
    {
        try {
            // 1. Delete associated physical images first
            foreach ($review->images as $image) {
                // Construct the full path using public_path since you stored them there
                $fullPath = public_path($image->image_path);
                
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
            
            // 2. Delete the record from the database
            // Note: The ReviewImage records will be deleted automatically if you have 
            // 'ON DELETE CASCADE' in your migration. If not, you might need $review->images()->delete(); here.
            $review->delete();

            return response()->json(['success' => true, 'message' => 'Review deleted successfully.']);

        } catch (\Exception $e) {
            Log::error('Review Destroy Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting review.'], 500);
        }
    }
}