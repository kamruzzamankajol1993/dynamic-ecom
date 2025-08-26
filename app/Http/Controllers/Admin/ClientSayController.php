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
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;
class ClientSayController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:clientSayView|clientSayAdd|clientSayUpdate|clientSayDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:clientSayAdd', ['only' => ['create','store']]);
         $this->middleware('permission:clientSayUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:clientSayDelete', ['only' => ['destroy']]);
    }

    public function index()
    {
        // Fetch all client sayings with pagination
        $clientsays = ClientSay::latest()->paginate(10); // Paginate with 10 items per page

        // Return the client_say index view, passing the clientsays data
        return view('admin.clientsay.index', compact('clientsays'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Return the view for creating a new client_say item
        return view('admin.clientsay.create');
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

        // Create a new ClientSay model instance and save it to the database
        ClientSay::create([
            'title' => $request->title,
            'des' => $request->des,
            'youtube_video_link' => $request->youtube_video_link,
            'status' => $request->status,
        ]);

        // Redirect back to the client_say index with a success message
        return redirect()->route('clientSay.index')->with('success', 'ClientSay entry added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientSay  $clientsay
     * @return \Illuminate\Http\Response
     */
    public function show(ClientSay $clientsay)
    {
        // This method can be used to show a single client_say item in detail.
        return view('admin.clientsay.show', compact('clientsay')); // Assuming you might create a show.blade.php
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClientSay  $clientsay
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $clientsay=ClientSay::find($id);
        // Return the view for editing the client_say item, passing the clientsay object
        return view('admin.clientsay.edit', compact('clientsay'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientSay  $clientsay
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $clientsay=ClientSay::find($id);
        // Validate the incoming request data
        $request->validate([
            'title' => 'required|string|max:255',
            'des' => 'required|string',
            'youtube_video_link' => 'nullable|url',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Update the ClientSay model instance
        $clientsay->update([
            'title' => $request->title,
            'des' => $request->des,
            'youtube_video_link' => $request->youtube_video_link,
            'status' => $request->status,
        ]);

        // Redirect back to the client_say index with a success message
        return redirect()->route('clientSay.index')->with('success', 'ClientSay entry updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientSay  $clientsay
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $clientsay=ClientSay::find($id);
        // Delete the ClientSay record from the database
        $clientsay->delete();

        // Redirect back to the client_say index with a success message
        return redirect()->route('clientSay.index')->with('success', 'ClientSay entry deleted successfully!');
    }
}
