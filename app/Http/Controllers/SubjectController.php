<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Year;
use App\Models\Stage;
use App\Models\SubjectYear;
use App\Models\User;
use App\Models\TeacherSubjectYear;
use App\Models\Category;

 use App\Http\Responses\ApiSuccessResponse;
 use App\Http\Responses\ApiErrorResponse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TeachersController;

class SubjectController extends Controller
{

    //**********************************************************************************************\/
    //show all subject in the category
  public function show_all_subjects(Request $request)
  {
      $category_id = $request->query('category_id');
      $subject = Subject::where('category_id', $category_id)
      ->get();
      return new ApiSuccessResponse(
        'this is the all subjects in the category.',
        $subject,
       201,
    );
  }
//**********************************************************************************************
//show all subjects in the category education
  public function all_subjects_in_year(Request $request)
    {
        $year_id = $request->query('year_id');
        $subject = Subject::whereHas('years_users', function($q) use ($year_id) {
            $q->where('teacher_subject_years.year_id', $year_id);
        })->get();
        $message = "this is the all subjects";
        return response()->json([
            'message' => $message,
            'data' => $subject]);
    }
    //***********************************************************************************************************************\\
    public function search_to_subject(Request $request)
{
    $request->validate([
        'category_id' => 'integer',
        'year_id' => 'integer',
        'name' => 'required|string',
    ]);

    $category_id = $request->query('category_id');
    $year_id = $request->query('year_id');
    $name = $request->query('name');

    if ($category_id == 1) { // if the category is educational
        $subjects = Subject::where('name', 'like', '%' . $name . '%')
            ->whereHas('years_users', function ($q) use ($year_id) {
                $q->where('teacher_subject_years.year_id', $year_id);
            })->get();
    } else {
        $subjects = Subject::where('name', 'like', '%' . $name . '%')
           // ->where('category_id', $category_id)//serach in one category
           ->where('category_id', '!=', 1)//search in all categoryes without educational
            ->get();
    }

    if ($subjects->isEmpty()) {
        return new ApiErrorResponse(
        'subject does not exist.',
       404,
        );

    }

    return response()->json([
        'message' => " this is the subjects .",
        'data' => $subjects,
    ]);
}

    //***********************************************************************************************************************\\
    public function add_subject(Request $request)
{
    $user = auth()->user();
    $request->validate([
        'category_id' => 'required',
        'name' => 'required',
        'price' => 'required',
        'description' => 'required',
        'image_data' ,
        'video_id' => 'integer',
        'file_id' => 'integer',
        'users_content' => 'required|array',
        'users_content.*.user_id' => 'required|integer',
        'years_content.*.year_id' => 'integer',
    ]);
     // Check if required fields are missing
     if (!$request->filled('category_id') || !$request->filled('users_content') || !$request->filled('users_content.0.user_id')) {
        return response()->json(['message' => 'Missing required fields.'], 400);
    }

    // Check if category exists
    $category = Category::find($request->input('category_id'));
    if (!$category) {
        return response()->json(['message' => 'Category not found.'], 404);
    }
    $subject = Subject::create([
        'name' => $request->name,
        'price' => $request->price,
        'description' => $request->description,
        'image_data' => $request->image_data,
        'video_id' => $request->video_id,
        'file_id' => $request->file_id,
        'category_id' => $request->category_id,
    ]);


        if ($request->category_id == 1) { // If the category is educational
            $yearsContent = $request->years_content;
            $usersContent = $request->users_content;

            foreach ($usersContent as $user) {
                foreach ($yearsContent as $year) {
                    $existingUser = User::find($user['user_id']);
                    if (!$existingUser) {
                    return response()->json(['message' => 'User not found.'], 404);
                        }

                    $existingYear = Year::find($year['year_id']);
                    if (!$existingYear) {
                    return response()->json(['message' => 'Year not found.'], 404);
                        }
                    $subject->years_users()->attach($user['user_id'], ['year_id' => $year['year_id']]);
                }
            }
        }else {
            foreach ($request->users_content as $user) {
                $existingUser = User::find($user['user_id']);
                if (!$existingUser) {
                return response()->json(['message' => 'User not found.'], 404);
                    }
                $subject->years_users()->attach($user['user_id']);
            }
        }

    return response()->json([
        'message' => 'Subject added successfully.',
        'data' => $subject,
    ]);
}

    //***********************************************************************************************************************\\
    public function edit_subject(Request $request)
    {
        $request->validate([
            'subject_id' => 'required',
            'category_id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'image_data' ,
            'video_id' => 'integer',
            'file_id' => 'integer',
            'users_content' => 'required|array',
            'users_content.*.user_id' => 'required|integer',
            'years_content.*.year_id' => 'integer',
        ]);

        $subject_id = $request->subject_id;
        $subject = Subject::find($subject_id);

        $subject->name = $request->name;
        $subject->price = $request->price;
        $subject->description = $request->description;
        $subject->category_id = $request->category_id;
        $subject->image_data = $request->image_data;
        $subject->video_id = $request->video_id;
        $subject->file_id = $request->file_id;
        $subject->save();


        if ($request->category_id == 1) { // if the category is educational
            $yearsContent = $request->years_content;
            $usersContent = $request->users_content;

            $subject->years_users()->detach();

            foreach ($usersContent as $user) {
                foreach ($yearsContent as $year) {
                    $existingUser = User::find($user['user_id']);
                    if (!$existingUser) {
                    return response()->json(['message' => 'User not found.'], 404);
                        }

                    $existingYear = Year::find($year['year_id']);
                    if (!$existingYear) {
                    return response()->json(['message' => 'Year not found.'], 404);
                        }
                    $subject->years_users()->attach($user['user_id'], ['year_id' => $year['year_id']]);
                }
            }
        }else {
            foreach ($request->users_content as $user) {
                $existingUser = User::find($user['user_id']);
                if (!$existingUser) {
                return response()->json(['message' => 'User not found.'], 404);
                    }
                $subject->years_users()->attach($user['user_id']);
            }
        }

        return response()->json([
            'message' => 'subject updated successfully',
            'data' => $subject,
        ]);
    }
    //***********************************************************************************************************************\\
 public function delete_subject($subject_id)
    {
        $user = auth()->user();
        $subject = Subject::find($subject_id);
        if (!$subject) {
            $message = "The subject doesn't exist.";
            return response()->json([
                'message' => $message,
            ]);
        }

        $subject->years_users()->detach();
        $subject->delete();

        $message = "The subject deleted successfully.";
        return response()->json([
            'message' => $message,
        ]);

    }
}
    //***********************************************************************************************************************\\
