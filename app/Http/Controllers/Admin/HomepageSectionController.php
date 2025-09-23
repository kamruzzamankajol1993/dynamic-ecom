<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Category;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;

class HomepageSectionController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 1)->pluck('name', 'id');
        $row1 = HomepageSection::where('row_identifier', 'row_1')->first();
        $row2 = HomepageSection::where('row_identifier', 'row_2')->first();

        return view('admin.homepage_section.index', compact('categories', 'row1', 'row2'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'row1_category_id' => 'nullable|exists:categories,id|different:row2_category_id',
            'row1_title' => 'nullable|string|max:255', // Added title validation
            'row1_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'row1_status' => 'nullable|boolean',
            'row2_category_id' => 'nullable|exists:categories,id',
            'row2_title' => 'nullable|string|max:255', // Added title validation
            'row2_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'row2_status' => 'nullable|boolean',
        ], [
            'row1_category_id.different' => 'The same category cannot be selected for both Row 1 and Row 2.',
        ]);

        // Pass the title field name to the helper function
        $this->processRow($request, 'row_1', 'row1_category_id', 'row1_image', 'row1_status', 'row1_title');
        $this->processRow($request, 'row_2', 'row2_category_id', 'row2_image', 'row2_status', 'row2_title');

        return redirect()->route('homepage-section.index')->with('success', 'Homepage sections updated successfully.');
    }

    private function processRow(Request $request, $rowIdentifier, $categoryField, $imageField, $statusField, $titleField)
    {
        $categoryId = $request->input($categoryField) ?: null;

        if (is_null($categoryId)) {
            $section = HomepageSection::where('row_identifier', $rowIdentifier)->first();
            if ($section) {
                $this->deleteImage($section->image);
                $section->delete();
            }
            return;
        }

        $section = HomepageSection::firstOrNew(['row_identifier' => $rowIdentifier]);
        
        $status = $request->has($statusField) ? 1 : 0;
        $title = $request->input($titleField); // Get title from request
        
        // Add title to the data array
        $data = ['category_id' => $categoryId, 'status' => $status, 'title' => $title];

        if ($request->hasFile($imageField)) {
            if ($section->image) {
                $this->deleteImage($section->image);
            }
            $data['image'] = $this->saveImage($request->file($imageField));
        }

        $section->fill($data)->save();
    }

    private function saveImage($imageFile)
    {
        $imageName = uniqid('section_') . '.' . $imageFile->getClientOriginalExtension();
        $destinationPath = public_path('homepage_sections');

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        Image::read($imageFile)->resize(410, 530)->save($destinationPath . '/' . $imageName);
        
        return 'public/homepage_sections/' . $imageName;
    }

    private function deleteImage($imagePath)
    {
        if (!$imagePath) return;
        
        $fullPath = public_path($imagePath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}