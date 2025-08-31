<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MainSlider;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class MainSliderController extends Controller
{
    public function index(): View
    {
        return view('admin.main_slider.index');
    }

    public function data(Request $request)
    {
        $query = MainSlider::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', $request->search . '%');
        }

        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $mainSliders = $query->paginate(10);

        return response()->json([
            'data' => $mainSliders->items(),
            'total' => $mainSliders->total(),
            'current_page' => $mainSliders->currentPage(),
            'last_page' => $mainSliders->lastPage(),
        ]);
    }

    public function create(): View
    {
        return view('admin.main_slider.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'main_slider_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/main_sliders');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            Image::read($image->getRealPath())->save($destinationPath . '/' . $imageName);
            $path = 'uploads/main_sliders/' . $imageName;
        }

        MainSlider::create([
            'title' => $request->title,
            'image' => $path,
        ]);

        return redirect()->route('main-slider.index')->with('success', 'Main Slider created successfully!');
    }

    public function edit(MainSlider $mainSlider): View
    {
        return view('admin.main_slider.edit', compact('mainSlider'));
    }

    public function update(Request $request, MainSlider $mainSlider)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|boolean',
        ]);

        $path = $mainSlider->image;
        if ($request->hasFile('image')) {
            if ($mainSlider->image && File::exists(public_path($mainSlider->image))) {
                File::delete(public_path($mainSlider->image));
            }
            $image = $request->file('image');
            $imageName = 'main_slider_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/main_sliders');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            
            Image::read($image->getRealPath())->save($destinationPath . '/' . $imageName);
            $path = 'uploads/main_sliders/' . $imageName;
        }

        $mainSlider->update([
            'title' => $request->title,
            'status' => $request->status,
            'image' => $path,
        ]);

        return redirect()->route('main-slider.index')->with('success', 'Main Slider updated successfully!');
    }

    public function destroy(MainSlider $mainSlider)
    {
        if ($mainSlider->image && File::exists(public_path($mainSlider->image))) {
            File::delete(public_path($mainSlider->image));
        }
        $mainSlider->delete();
        return response()->json(['message' => 'Main Slider deleted successfully']);
    }
}
