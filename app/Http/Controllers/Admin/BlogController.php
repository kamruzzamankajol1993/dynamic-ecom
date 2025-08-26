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
use App\Models\Blog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;
class BlogController extends Controller
{
     function __construct()
    {
         $this->middleware('permission:blogView|blogAdd|blogUpdate|blogDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:blogAdd', ['only' => ['create','store']]);
         $this->middleware('permission:blogUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:blogDelete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $blogs = Blog::latest()->paginate(10);
        return view('admin.blog.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.blog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 1. Validate the request
        $request->validate([
            'title' => 'required|string|max:255|unique:blogs,title',
            'des' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:Active,inactive',
        ]);

        // 2. Prepare data
        $data = $request->except('image');
        $data['slug'] = Str::slug($request->title, '-');

        // 3. Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $data['slug'] . '-' . time() . '.' . $image->getClientOriginalExtension();
            
            // Create directory if it doesn't exist
            $path = public_path('uploads/blogs');
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }

            // Save image using Intervention Image
            Image::read($image)->save($path.'/'.$imageName);

            $data['image'] = 'public/uploads/blogs/' . $imageName;
        }

        // 4. Create the blog post
        Blog::create($data);

        // 5. Redirect with a success message
        return redirect()->route('blog.index')->with('success', 'Blog post created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $blog = Blog::find($id);
        return view('admin.blog.edit', compact('blog'));
    }

    public function show($id)
    {
        $blog = Blog::find($id);
        return view('admin.blog.show', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
         $blog = Blog::find($id);
        // 1. Validate the request
        $request->validate([
            'title' => 'required|string|max:255|unique:blogs,title,' . $blog->id,
            'des' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:Active,inactive',
        ]);

        // 2. Prepare data
        $data = $request->except('image');
        $data['slug'] = Str::slug($request->title, '-');

        // 3. Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete old image
            if (File::exists(public_path($blog->image))) {
                File::delete(public_path($blog->image));
            }

            $image = $request->file('image');
            $imageName = $data['slug'] . '-' . time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/blogs');

            Image::read($image)->save($path.'/'.$imageName);

            $data['image'] = 'public/uploads/blogs/' . $imageName;
        }

        // 4. Update the blog post
        $blog->update($data);

        // 5. Redirect with success message
        return redirect()->route('blog.index')->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

         $blog = Blog::find($id);
        // Delete the image file
        if (File::exists(public_path($blog->image))) {
            File::delete(public_path($blog->image));
        }

        // Delete the blog post from the database
        $blog->delete();

        return redirect()->route('blog.index')->with('success', 'Blog post deleted successfully.');
    }
}
