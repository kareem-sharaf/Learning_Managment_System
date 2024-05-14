<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\Year;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    //  index all categories
    public function index()
    {
        $categories = Category::all();
        if ($categories) {
            return response()->json(
                [
                    'message' => 'success',
                    'Categories:' => $categories
                ],
                200
            );
        }
        return response()->json(
            ['message' => 'no categories found!'],
            404
        );
    }

    //  show the subjects of a specific category
    public function show(Request $request)
    {
        $category = Category::where('category', $request->category)->first();

        if (!$category) {
            return response()->json(
                ['message' => 'Category not found!'],
                404
            );
        }

        $subjects = $category->subjects;
        if (!$subjects) {
            return response()->json(
                ['message' => 'No subjects found in this Category!'],
                404
            );
        }

        return response()->json(
            [
                'message' => 'Subjects of this category:',
                'subjects' => $subjects
            ],
            200
        );
    }

    //  search in categories
    public function search(Request $request)
    {
        $categoryName = $request->query('category');

        $categories = Category::query();

        if ($categoryName) {
            $categories->where('category', 'like', '%' . $categoryName . '%');
        }

        $result = $categories->get();

        if ($result->isNotEmpty()) {
            return response()->json(
                ['message' => $result],
                200
            );
        }

        return response()->json(
            ['message' => 'No categories found!'],
            404
        );
    }


    //  store new category
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'category' => 'required|string|unique:categories',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',

        ]);

        $imagePath = $request->file('image')->store('category_images', 'public');

        $imageUrl = Storage::url($imagePath);

        $category = Category::create([
            'category' => $request->category,
            'image_url' => $imageUrl,
        ]);

        return response()->json(
            [
                'message' => 'Categories created successfully',
                'category' => $category
            ],
            201
        );
    }

    // update category
    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'category' => 'required|string|exists:categories,category',
            'new_category' => 'string|unique:categories,category',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $category = Category::where('category', $request->category)
            ->first();

        if (!$category) {
            return response()->json(
                ['message' => 'Category not found'],
                404
            );
        }

        if ($request->has('new_category')) {
            $category->category = $request->new_category;
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('category_images', 'public');
            $imageUrl = Storage::url($imagePath);

            if ($category->image_url) {

                $oldImagePath = str_replace('/storage', 'public', $category->image_url);

                Storage::delete($oldImagePath);
            }

            $category->image_url = $imageUrl;
        }

        $category->save();

        return response()->json(
            [
                'message' => 'Category updated successfully',
                'category' => $category
            ],
            200
        );
    }

    //  show soft deleted categories
    public function showSoftDeleted()
    {
        $user = Auth::user();
        $softDeletedCategories = Category::onlyTrashed()->get();

        if ($softDeletedCategories->isEmpty()) {
            return response()->json(
                ['message' => 'No soft deleted categories found'],
                404
            );
        }

        return response()->json(
            ['soft_deleted_categories' => $softDeletedCategories],
            200
        );
    }

    //  soft delete category
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $category = Category::where('category', $request->category)->first();

        if (!$category) {
            return response()->json(
                ['message' => 'Category not found!'],
                404
            );
        }

        $category->delete();

        return response()->json(
            ['message' => 'Category deleted successfully'],
            200
        );
    }

    //  force delete category
    public function forceDelete(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'category' => 'required|string|exists:categories',
        ]);

        $category = Category::where('category', $request->category)->withTrashed()->first();

        if (!$category) {
            return response()->json(
                ['message' => 'Category not found!'],
                404
            );
        }

        if ($category->image_url) {
            $imagePath = str_replace('/storage', 'public', $category->image_url);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $category->forceDelete();

        return response()->json(
            ['message' => 'Category permanently deleted successfully'],
            200
        );
    }

    // seed categories
    public function seedCategory()
    {
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
    }
}
