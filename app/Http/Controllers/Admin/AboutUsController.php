<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use Mpdf\Mpdf;
use App\Models\AboutUs;
use App\Models\Blog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;
class AboutUsController extends Controller
{
    public function index()
    {
        $aboutUs = AboutUs::first();
        return view('admin.aboutUs.manage', compact('aboutUs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'youtube_video_link' => 'nullable|url',
            'des' => 'required|string',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'about-us-' . time() . '.' . $image->getClientOriginalExtension();
            
            $path = public_path('uploads/about');
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }

            Image::read($image)->save($path.'/'.$imageName);

            $data['image'] = 'public/uploads/about/' . $imageName;
        }

        AboutUs::create($data);

        return redirect()->route('aboutUs.index')->with('success', 'About Us content created successfully.');
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'youtube_video_link' => 'nullable|url',
            'des' => 'required|string',
        ]);

        $aboutUs = AboutUs::findOrFail($id);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if (File::exists(public_path($aboutUs->image))) {
                File::delete(public_path($aboutUs->image));
            }

            $image = $request->file('image');
            $imageName = 'about-us-' . time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/about');

            Image::read($image)->save($path.'/'.$imageName);

            $data['image'] = 'public/uploads/about/' . $imageName;
        }

        $aboutUs->update($data);

        return redirect()->route('aboutUs.index')->with('success', 'About Us content updated successfully.');
    }
}
