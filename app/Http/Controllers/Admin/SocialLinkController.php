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
use App\Models\SocialLink;
use App\Models\NewsAndMedia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;
class SocialLinkController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:socialLinkView|socialLinkAdd|socialLinkUpdate|socialLinkDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:socialLinkAdd', ['only' => ['create','store']]);
         $this->middleware('permission:socialLinkUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:socialLinkDelete', ['only' => ['destroy']]);
    }

    // Define a list of common social media names for consistent options
    private $socialMediaNames = [
        'Facebook', 'Twitter', 'Instagram', 'LinkedIn', 'YouTube', 'TikTok',
        'Pinterest', 'Snapchat', 'Reddit', 'WhatsApp', 'Telegram', 'Vimeo',
        'GitHub', 'Stack Overflow', 'Flickr', 'Tumblr', 'Discord',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch all social links with pagination
        $socialLinks = SocialLink::latest()->paginate(10); // Paginate with 10 items per page

        // Return the social_link index view, passing the data
        return view('admin.social_link.index', compact('socialLinks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Pass the social media names to the create view
        $socialMediaNames = $this->socialMediaNames;
        return view('admin.social_link.create', compact('socialMediaNames'));
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
            'title' => 'required|string|in:' . implode(',', $this->socialMediaNames) . '|unique:social_links,title', // Ensure title is from the predefined list and unique
            'link' => 'required|url|max:255',
        ], [
            'title.in' => 'Please select a valid social media name from the list.',
            'title.unique' => 'A social link for this platform already exists. Please edit the existing one or choose a different platform.',
            'link.url' => 'The link must be a valid URL.',
        ]);

        // Create a new SocialLink model instance and save it to the database
        SocialLink::create([
            'title' => $request->title,
            'link' => $request->link,
        ]);

        // Redirect back to the index with a success message
        return redirect()->route('socialLink.index')->with('success', 'Social link added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SocialLink  $socialLink
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SocialLink  $socialLink
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $socialLink = SocialLink::find($id);
        // Pass the social media names and the current socialLink object to the edit view
        $socialMediaNames = $this->socialMediaNames;
        return view('admin.social_link.edit', compact('socialLink', 'socialMediaNames'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SocialLink  $socialLink
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $socialLink = SocialLink::find($id);
        // Validate the incoming request data
        $request->validate([
            'title' => 'required|string|in:' . implode(',', $this->socialMediaNames) . '|unique:social_links,title,' . $socialLink->id, // Unique except for itself
            'link' => 'required|url|max:255',
        ], [
            'title.in' => 'Please select a valid social media name from the list.',
            'title.unique' => 'A social link for this platform already exists. Please choose a different platform.',
            'link.url' => 'The link must be a valid URL.',
        ]);

        // Update the SocialLink model instance
        $socialLink->update([
            'title' => $request->title,
            'link' => $request->link,
        ]);

        // Redirect back to the index with a success message
        return redirect()->route('socialLink.index')->with('success', 'Social link updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SocialLink  $socialLink
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $socialLink = SocialLink::find($id);
        // Delete the SocialLink record from the database
        $socialLink->delete();

        // Redirect back to the index with a success message
        return redirect()->route('socialLink.index')->with('success', 'Social link deleted successfully!');
    }
}
