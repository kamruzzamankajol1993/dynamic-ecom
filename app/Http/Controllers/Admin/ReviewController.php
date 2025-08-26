<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use Mpdf\Mpdf;
use App\Models\ClientSay;
use App\Models\Review;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;
class ReviewController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:reviewView|reviewAdd|reviewUpdate|reviewDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:reviewAdd', ['only' => ['create','store']]);
         $this->middleware('permission:reviewUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:reviewDelete', ['only' => ['destroy']]);
    }

    public function index()
    {
        // Fetch all client sayings with pagination
        $reviews = Review::latest()->paginate(10); // Paginate with 10 items per page

        // Return the client_say index view, passing the reviews data
        return view('admin.review.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Return the view for creating a new client_say item
        return view('admin.review.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'title' => 'required|string|max:255',
            'des' => 'required|string',
            'youtube_video_link' => 'nullable|url', // YouTube link is optional and must be a valid URL
            'status' => 'required|in:Active,Inactive',
        ]);

        // Create a new review model instance and save it to the database
        Review::create([
            'title' => $request->title,
            'des' => $request->des,
            'youtube_video_link' => $request->youtube_video_link,
            'status' => $request->status,
        ]);

        // Redirect back to the client_say index with a success message
        return redirect()->route('review.index')->with('success', 'review entry added successfully!');
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $review=Review::find($id);
        // Return the view for editing the client_say item, passing the review object
        return view('admin.review.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $review=Review::find($id);
        // Validate the incoming request data
        $request->validate([
            'title' => 'required|string|max:255',
            'des' => 'required|string',
            'youtube_video_link' => 'nullable|url',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Update the review model instance
        $review->update([
            'title' => $request->title,
            'des' => $request->des,
            'youtube_video_link' => $request->youtube_video_link,
            'status' => $request->status,
        ]);

        // Redirect back to the client_say index with a success message
        return redirect()->route('review.index')->with('success', 'review entry updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $review=Review::find($id);
        // Delete the review record from the database
        $review->delete();

        // Redirect back to the client_say index with a success message
        return redirect()->route('review.index')->with('success', 'review entry deleted successfully!');
    }
}
