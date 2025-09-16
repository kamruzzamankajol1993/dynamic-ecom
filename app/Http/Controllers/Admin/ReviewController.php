<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use App\Models\ProductReviewImage; 
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ReviewController extends Controller
{
    public function index()
    {
        return view('admin.reviews.index');
    }

    public function data(Request $request)
    {
        // Now using 'user' relationship as established before
        $query = ProductReview::with(['product:id,name', 'user:id,name']);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            // Changed search from 'comment' to 'description'
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

    public function update(Request $request, ProductReview $review)
    {
        $request->validate([
            // Changed validation from 'comment' to 'description'
            'description' => 'nullable|string',
            // Changed validation from 'published' to 'is_approved'
            'is_approved' => 'required|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $review->update([
            // Changed update key from 'comment' to 'description'
            'description' => $request->description,
            // Changed update key from 'published' to 'is_approved'
            'is_approved' => $request->is_approved,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imageName = 'review-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = 'review_images/' . $imageName;

                $image = Image::make($file)->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode();

                Storage::disk('public')->put($path, $image);
                
                $review->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('review.index')->with('success', 'Review updated successfully.');
    }

    public function destroy(ProductReview $review)
    {
        foreach ($review->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $review->delete();
        return response()->json(['message' => 'Review deleted successfully.']);
    }
}