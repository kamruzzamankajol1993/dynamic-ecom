<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use Mpdf\Mpdf;
use App\Models\User;
use App\Models\Customer;
use App\Models\Slider;
use App\Models\Gallery;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;
class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $galleries = Gallery::all();
        // Assuming you have an 'admin.gallery.index.blade.php' for listing gallerys.
        return view('admin.gallery.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg', // 'image' is required for create
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $input = $request->except('_token');

        // Handle image upload and compression with Intervention Image
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '.' . $imageFile->getClientOriginalExtension();
            $destinationPath = public_path('uploads/galleries'); // Changed destination folder

            // Create directory if it doesn't exist
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $img = Image::read($imageFile->getRealPath());

          

            // Compress image to fit 500KB (approx)
            $maxFileSize = 500 * 1024; // 500 KB in bytes
            $quality = 90;
            $imgFormat = $imageFile->getClientOriginalExtension();

            do {
                if ($imgFormat === 'png') {
                    $img->save($destinationPath . '/' . $imageName, $quality);
                } else {
                    $img->save($destinationPath . '/' . $imageName, $quality);
                }
                clearstatcache();
                $currentSize = File::size($destinationPath . '/' . $imageName);
                $quality -= 5;
            } while ($currentSize > $maxFileSize && $quality >= 10);

            if ($currentSize > $maxFileSize) {
                \Log::warning("Slider Image '{$imageName}' could not be compressed to under 500KB. Current size: " . round($currentSize / 1024, 2) . "KB");
            }

            $input['image'] = 'public/uploads/galleries/' . $imageName; // Save path in database
        }

        Gallery::create($input); // Changed model

        return redirect()->route('gallery.index')->with('success', 'Slider created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slider  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $gallery) // Changed model variable
    {
        return view('admin.gallery.show', compact('gallery'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slider  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit($id) // Changed model variable
    {
        $gallery = Gallery::find($id);
        return view('admin.gallery.edit', compact('gallery'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Slider  $gallery
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id) // Changed model variable
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg', // 'image' is nullable for update
            'status' => 'required|string|in:Active,Inactive',
        ]);
$gallery = Gallery::find($id);
        $input = $request->except('_token', '_method');

        // Handle image update and compression with Intervention Image
        if ($request->hasFile('image')) {
            // Delete old image if it exists in the public/uploads folder
            if ($gallery->image && File::exists(public_path($gallery->image))) { // Changed model variable
                File::delete(public_path($gallery->image)); // Changed model variable
            }

            $imageFile = $request->file('image');
            $imageName = time() . '.' . $imageFile->getClientOriginalExtension();
            $destinationPath = public_path('uploads/galleries'); // Changed destination folder

            // Create directory if it doesn't exist
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $img = Image::read($imageFile->getRealPath());

           

            // Compress image to fit 500KB (approx)
            $maxFileSize = 500 * 1024; // 500 KB in bytes
            $quality = 90;
            $imgFormat = $imageFile->getClientOriginalExtension();

            do {
                if ($imgFormat === 'png') {
                    $img->save($destinationPath . '/' . $imageName, $quality);
                } else {
                    $img->save($destinationPath . '/' . $imageName, $quality);
                }
                clearstatcache();
                $currentSize = File::size($destinationPath . '/' . $imageName);
                $quality -= 5;
            } while ($currentSize > $maxFileSize && $quality >= 10);

            if ($currentSize > $maxFileSize) {
                 \Log::warning("Slider Image '{$imageName}' could not be compressed to under 500KB during update. Current size: " . round($currentSize / 1024, 2) . "KB");
            }

            $input['image'] = 'public/uploads/galleries/' . $imageName; // Save new path in database
        } else {
            // If no new image, retain the old one
            $input['image'] = $gallery->image; // Changed model variable
        }

        $gallery->update($input); // Changed model

        return redirect()->route('gallery.index')->with('success', 'Slider updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Slider  $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) // Changed model variable
    {

        //dd($id);

        $gallery = Gallery::find($id);
        // Delete associated image from public/uploads folder if it exists
        if ($gallery->image && File::exists(public_path($gallery->image))) { // Changed model variable
            File::delete(public_path($gallery->image)); // Changed model variable
        }

        $gallery->delete(); // Changed model

        return redirect()->route('gallery.index')->with('success', 'Slider deleted successfully!');
    }
}
