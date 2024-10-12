<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Lesson;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use App\Services\SubjectService;
use App\Services\UserService;
use App\Services\UnitService;
use App\Services\LessonService;
use App\Services\ImageService;
use App\Services\VideoService;
use App\Services\FileService;

use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

use Illuminate\Http\Response;

class CategoryController extends Controller
{
    //******************************************************************************************* */

    protected $categoryService;
    protected $userService;
    protected $unitService;
    protected $lessonService;
    protected $subjectService;
    protected $imageService;
    protected $videoService;
    protected $fileService;

    public function __construct(
        CategoryService $categoryService,
        UserService $userService,
        UnitService $unitService,
        LessonService $lessonService,
        SubjectService $subjectService,
        ImageService $imageService,
        VideoService $videoService,
        FileService $fileService

    ) {
        $this->categoryService = $categoryService;
        $this->userService = $userService;
        $this->unitService = $unitService;
        $this->lessonService = $lessonService;
        $this->subjectService = $subjectService;
        $this->imageService = $imageService;
        $this->videoService = $videoService;
        $this->fileService = $fileService;
    }
    //******************************************************************************************* */
    //  index all categories
    public function index()
    {
        $categories = Category::where('exist', true)->get();

        if ($categories->isEmpty()) {
            return new ApiErrorResponse('no categories found!', Response::HTTP_NOT_FOUND);
        }

        return new ApiSuccessResponse('this is the all Categories', $categories);
    }

    //******************************************************************************************* */
    //  show specific category
    public function showOne($category_id)
    {
        return $this->categoryService->getCategory($category_id);
    }
    //******************************************************************************************* */
    /*search in subjects,category,teachers,unit and lessons.
    in case the user has year_id will see just one subject in his year else he will see all subjects.*/
    public function search(Request $request)
    {
        $name = $request->query('name');

        if (!$name) {
            return response()->json([
                'message' => "There is nothing to search.",
            ]);
        }

        $categories = $this->categoryService->search($name);
        $teachers = $this->userService->search($name);
        $units = $this->unitService->search($name);
        $lessons = $this->lessonService->search($name);
        $subjects = $this->subjectService->search($name);

        return response()->json([
            'message' => "There are the items.",
            'categories' => $categories,
            'teachers' => $teachers,
            'units' => $units,
            'lessons' => $lessons,
            'subjects' => $subjects,
        ]);
    }
    //******************************************************************************************* */


    //  store new category
    public function store(CategoryRequest $request)
    {

        $data = $request->validated();
        // Handle image upload
        $data['image'] = $this->imageService->uploadImage($request->file('image'), 'categories_images');

        $category = Category::create($data);
        return response()->json(
            [
                'message' => 'Category created successfully',
                'category' => $category
            ],
            201
        );
    }


    //******************************************************************************************* */
    // update category
    public function update(CategoryRequest $request, $category_id)
    {

        $data = $request->validated();

        $category = $this->categoryService->getCategory($category_id);

        if (!$category) {
            return response()->json([
                'message' => 'Category does not exist!.',
            ], 404);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageService->replaceImage($request->file('image'), $category->image, 'categories_images');
        }
        $category->update($data);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ], 200);
    }
    //******************************************************************************************* */

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
    public function destroy(Request $request) //*
    {
        $user = Auth::user();
        $category_id = $request->category_id;
        $category = Category::find($category_id);

        if ($category) {
            $category->update(['exist' => false]);

            Subject::where('category_id', $category->id)
                ->update(['exist' => false]);

            Unit::whereIn('subject_id', function ($query) use ($category) {
                $query->select('id')
                    ->from('subjects')
                    ->where('category_id', $category->id);
            })->update(['exist' => false]);

            Lesson::whereIn('unit_id', function ($query) use ($category) {
                $query->select('id')
                    ->from('units')
                    ->whereIn('subject_id', function ($query) use ($category) {
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
