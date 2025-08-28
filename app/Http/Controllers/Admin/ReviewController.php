<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return view('admin.reviews.index');
    }

    public function data(Request $request)
    {
        $query = Review::with(['product:id,name', 'customer:id,name']);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('comment', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('product', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('customer', function ($q) use ($searchTerm) {
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

    public function show(Review $review)
    {
        $review->load(['product', 'customer']);
        return view('admin.reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        $review->load(['product', 'customer']);
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        $request->validate([
            'comment' => 'nullable|string',
            'published' => 'required|boolean',
        ]);

        $review->update([
            'comment' => $request->comment,
            'published' => $request->published,
        ]);

        return redirect()->route('admin.review.index')->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return response()->json(['message' => 'Review deleted successfully.']);
    }
}