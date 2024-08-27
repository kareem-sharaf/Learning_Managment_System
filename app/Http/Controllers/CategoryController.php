<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Lesson;
use Illuminate\Support\str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    //  index all categories
    public function index()
    {
        $categories = Category::where('exist', true)->get();
        if ($categories) {
            return response()->json($categories,200);
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
        // $user = Auth::user();
        $request->validate([
            'category' => 'required|string|unique:categories',
            'image' => 'required|image'
        ]);

        $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('categories/image', $request->image, $imageName);
        $category=Category::create($request->post()+ ['image'=> $imageName]);


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
            'category_id' => 'required|integer|exists:categories,id',
            'category' => 'string|unique:categories,category',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $category = Category::where('id', $request->category_id)
            ->first();

        if (!$category) {
            return response()->json(
                ['message' => 'Category not found'],
                404
            );
        }

        if ($request->has('category')) {
            $category->category = $request->category;
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
        $category_id=$request->category_id;
        $category = Category::find($category_id);

        if ($category) {
            $category->update(['exist' => false]);

            Subject::where('category_id', $category->id)
                   ->update(['exist' => false]);

            Unit::whereIn('subject_id', function($query) use ($category) {
                $query->select('id')
                      ->from('subjects')
                      ->where('category_id', $category->id);
            })->update(['exist' => false]);

            Lesson::whereIn('unit_id', function($query) use ($category) {
                $query->select('id')
                      ->from('units')
                      ->whereIn('subject_id', function($query) use ($category) {
                          $query->select('id')
                                ->from('subjects')
                                ->where('category_id', $category->id);
                      });
            })->update(['exist' => false]);

            return response()->json(['message' => 'Category and related items have been deleted successfuly.']);
        } else {
            return response()->json(['message' => 'Category not found.'], 404);
        }
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
