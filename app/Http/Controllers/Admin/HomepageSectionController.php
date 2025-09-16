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
    /**
     * Display the single page for managing homepage sections.
     */
    public function index()
    {
        $categories = Category::where('status', 1)->pluck('name', 'id');
        $row1 = HomepageSection::where('row_identifier', 'row_1')->first();
        $row2 = HomepageSection::where('row_identifier', 'row_2')->first();

        return view('admin.homepage_section.index', compact('categories', 'row1', 'row2'));
    }

    /**
     * Update the settings for both rows from a single form submission.
     */
    public function update(Request $request)
    {
        $request->validate([
            'row1_category_id' => 'nullable|exists:categories,id|different:row2_category_id',
            'row1_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'row2_category_id' => 'nullable|exists:categories,id',
            'row2_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'row1_category_id.different' => 'The same category cannot be selected for both Row 1 and Row 2.',
        ]);

        $this->processRow($request, 'row_1', 'row1_category_id', 'row1_image');
        $this->processRow($request, 'row_2', 'row2_category_id', 'row2_image');

        return redirect()->route('homepage-section.index')->with('success', 'Homepage sections updated successfully.');
    }

    /**
     * Helper function to process data for a given row.
     */
    private function processRow(Request $request, $rowIdentifier, $categoryField, $imageField)
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
        $data = ['category_id' => $categoryId];

        if ($request->hasFile($imageField)) {
            if ($section->image) {
                $this->deleteImage($section->image);
            }
            $data['image'] = $this->saveImage($request->file($imageField));
        }
        
        // REMOVED: The 'elseif' block for handling the remove image checkbox has been deleted.

        $section->fill($data)->save();
    }

    /**
     * Helper function to save a resized image.
     */
    private function saveImage($imageFile)
    {
        $imageName = uniqid('section_') . '.' . $imageFile->getClientOriginalExtension();
        $destinationPath = public_path('homepage_sections');

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        Image::read($imageFile)->resize(410, 530)->save($destinationPath . '/' . $imageName);
        
        // Return a path relative to the public directory
        return 'public/homepage_sections/' . $imageName;
    }

    /**
     * Helper function to delete an image file.
     */
    private function deleteImage($imagePath)
    {
        if (!$imagePath) return;
        
        // Construct the full path from the public directory
        $fullPath = public_path($imagePath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}