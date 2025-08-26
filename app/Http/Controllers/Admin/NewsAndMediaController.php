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
use App\Models\NewsAndMedia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;
class NewsAndMediaController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:newsAndMediaView|newsAndMediaAdd|newsAndMediaUpdate|newsAndMediaDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:newsAndMediaAdd', ['only' => ['create','store']]);
         $this->middleware('permission:newsAndMediaUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:newsAndMediaDelete', ['only' => ['destroy']]);
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch all news and media items with pagination
        $newsAndMedia = NewsAndMedia::latest()->paginate(10); // Paginate with 10 items per page

        // Return the news_and_media index view, passing the data
        return view('admin.news_and_media.index', compact('newsAndMedia'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Return the view for creating a new news and media item
        return view('admin.news_and_media.create');
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB, required for create
            'status' => 'required|in:Active,Inactive',
        ]);

        $imagePath = null;
        // Handle image upload with Image Intervention
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('upload/news_media_images'); // Define destination directory

            // Create directory if it doesn't exist
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            // Use Image Intervention to resize and save the image
            Image::make($image)->save($destinationPath . '/' . $imageName);

            $imagePath = 'public/upload/news_media_images/' . $imageName;
        }

        // Create a new NewsAndMedia model instance and save it to the database
        NewsAndMedia::create([
            'title' => $request->title,
            'des' => $request->des,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        // Redirect back to the index with a success message
        return redirect()->route('newsAndMedia.index')->with('success', 'News and Media entry added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NewsAndMedia  $newsAndMedia
     * @return \Illuminate\Http\Response
     */
 

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NewsAndMedia  $newsAndMedia
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $newsAndMedia = NewsAndMedia::find($id);
        // Return the view for editing the news and media item, passing the object
        return view('admin.news_and_media.edit', compact('newsAndMedia'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NewsAndMedia  $newsAndMedia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $newsAndMedia = NewsAndMedia::find($id);
        // Validate the incoming request data
        $request->validate([
            'title' => 'required|string|max:255',
            'des' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image is nullable for update
            'status' => 'required|in:Active,Inactive',
        ]);

        $imagePath = $newsAndMedia->image; // Keep existing image path by default

        // Handle image upload with Image Intervention if a new file is present
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($newsAndMedia->image && File::exists(public_path($newsAndMedia->image))) {
                File::delete(public_path($newsAndMedia->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('upload/news_media_images'); // Define destination

            // Use Image Intervention to resize and save the new image
            Image::make($image)->save($destinationPath . '/' . $imageName);

            $imagePath = 'public/upload/news_media_images/' . $imageName;
        }

        // Update the NewsAndMedia model instance
        $newsAndMedia->update([
            'title' => $request->title,
            'des' => $request->des,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        // Redirect back to the index with a success message
        return redirect()->route('newsAndMedia.index')->with('success', 'News and Media entry updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NewsAndMedia  $newsAndMedia
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Delete the image file from storage if it exists
        if ($newsAndMedia->image && File::exists(public_path($newsAndMedia->image))) {
            File::delete(public_path($newsAndMedia->image));
        }

        $newsAndMedia = NewsAndMedia::find($id);

        // Delete the NewsAndMedia record from the database
        $newsAndMedia->delete();

        // Redirect back to the index with a success message
        return redirect()->route('newsAndMedia.index')->with('success', 'News and Media entry deleted successfully!');
    }
}
