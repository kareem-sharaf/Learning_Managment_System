<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Lesson;
use App\Models\User;

use Illuminate\Support\str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;

use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }




    //  index all categories
    public function index()
{
    return $this->categoryService->index();
}

    //  show the subjects of a specific category
    public function show(Request $request)
{
    $category_id = $request->category_id;
    $year_id = $request->year_id;

    $category = $this->getCategory($category_id);
    if (!$category) {
        return response()->json(['message' => 'Category not found!'], 404);
    }

    if (!$this->validateCategoryYear($category_id, $year_id)) {
        return response()->json(['message' => 'Please select a year!'], 400);
    }

    $subjects = $this->getSubjects($category_id, $year_id, $category);
    if ($subjects->isEmpty()) {
        return response()->json(['message' => 'No subjects found in this category!'], 404);
    }

    $subjects_with_users = $this->attachUsersToSubjects($subjects);

    return response()->json([
        'message' => 'Category and its subjects with users:',
        'category' => $category,
        'subjects' => $subjects_with_users,
    ], 200);
}



private function getCategory($category_id)
{
    return Category::where('id', $category_id)->first();
}

private function validateCategoryYear($category_id, $year_id)
{
    if ($category_id == 1 && !$year_id) {
        return false;
    }
    return true;
}

private function getSubjects($category_id, $year_id, $category)
{
    if ($category_id == 1 && $year_id) {
        return Subject::where('category_id', $category->id)
            ->whereHas('years_users', function ($query) use ($year_id) {
                $query->where('teacher_subject_years.year_id', $year_id);
            })
            ->where('exist', true)
            ->get();
    }

    return Subject::where('category_id', $category_id)
        ->where('exist', true)
        ->get();
}

private function attachUsersToSubjects($subjects)
{
    return $subjects->map(function($subject) {
        $subjectUsers = User::whereIn('id', function($query) use ($subject) {
            $query->select('user_id')
                ->from('teacher_subject_years')
                ->where('subject_id', $subject->id);
        })->get();

        $subject->users = $subjectUsers;
        return $subject;
    });
}










//  store new category
public function store(CategoryRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $category = $this->categoryService->createCategory($data);
        return response()->json(
            [
                'message' => 'Category created successfully',
                'category' => $category
            ]
        );
    }





    // update category
    public function update(CategoryRequest $request , $category_id)
{
    $data = $request->validated();
    $category = $this->categoryService->editCategory($data,$category_id);
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
    public function destroy(Request $request)//*
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
