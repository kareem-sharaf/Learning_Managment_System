<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Year;
use App\Models\Stage;
use App\Models\SubjectYear;
use App\Models\User;
use App\Models\TeacherSubjectYear;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\Unit;
use App\Models\Subscription;

 use App\Http\Responses\ApiSuccessResponse;
 use App\Http\Responses\ApiErrorResponse;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\Storage;
 use Illuminate\Support\Facades\File;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TeachersController;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{

    //**********************************************************************************************\/
    /*show all subjects in the category and in case the category is educational we will
     need year_id and if we don't have year_id we will show the years.*/
  public function show_all_subjects(Request $request)
  {
      $category_id = $request->query('category_id');
      $year_id = $request->query('year_id');
      $subject=null;
      $year=null;
      if($category_id==1 && $year_id){
        $subject = Subject::whereHas('years_users', function($q) use ($year_id) {
            $q->where('teacher_subject_years.year_id', $year_id);
        })->get();
      }else if($category_id==1 && !$year_id){
        $year = Year::get();
      } else {
        $subject = Subject::where('category_id', $category_id)
      ->get();
      }
    $message='this is the all subjects in the category.';
    return response()->json([
        'message' => $message,
        'data' => $subject,
        'year' => $year,
    ]);
  }
    //*********************************************************************************************** */
    /*user can choose the year and show the subjects. */
    public function all_subjects_in_year(Request $request)
  {
      $year_id = $request->query('year_id');
        $subject = Subject::whereHas('years_users', function($q) use ($year_id) {
            $q->where('teacher_subject_years.year_id', $year_id);
        })->get();
    $message='this is the all subjects in the year.';
    return response()->json([
        'message' => $message,
        'data' => $subject,
    ]);
  }
    //*********************************************************************************************** */
    /*user can choose the subject and show the details. */
    public function show_one_subject(Request $request)
  {
      $subject_id = $request->query('subject_id');
      $subject = Subject::where('id',$subject_id)->get();
      $message='this is the subjects details.';
      return response()->json([
         'message' => $message,
         'data' => $subject,
      ]);
  }
    //*********************************************************************************************** */
    /*show all categories and subjects and teachers.
    we show the all categories and subjects and teachers(role_id = 3)
    if category is educational if the user has year_id we show the subjects in the year else we show the years.*/
    public function index(Request $request)
    {
        $year_id = $request->query('year_id');
        $categories = Category::all();

        $categoriesWithSubjects = [];

        foreach ($categories as $category) {
            $categoryData = [
                'category' => $category,
                'subjects' => [],
                'years' => []
            ];

            if ($category->id == 1 && $year_id) {
                $categoryData['subjects'] = Subject::where('category_id', $category->id)
                ->whereHas('years_users', function($query) use ($year_id) {
                    $query->where('teacher_subject_years.year_id', $year_id);
                })
                ->get();
            }else if($category->id == 1 && !$year_id){
                $categoryData['years'] = Year::get();
            } else {
                $categoryData['subjects'] = Subject::where('category_id', $category->id)
                    // ->whereNotIn('category_id', [1])
                    ->get();
            }

            foreach ($categoryData['subjects'] as $subject) {
                $subject->users = Subject::whereHas('years_users', function($query) use ($subject) {
                    $query->where('subject_id', $subject->id);
                })->get();

                $subject->users = User::whereIn('id', function($query) use ($subject) {
                    $query->select('user_id')->from('teacher_subject_years')->where('subject_id', $subject->id);
                })->get();
            }

            $categoriesWithSubjects[] = $categoryData;
        }

        $message = "this is the all data";
        return response()->json([
            'message' => $message,
            'data' => $categoriesWithSubjects
        ]);
    }
//****************************************************************************************************************** */
//search just in subjects.
public function search_in_subjects(Request $request)
{
    $year_id = $request->query('year_id');
    $name = $request->query('name');

    $subjects= [];
    if($name){

        if($year_id){
            $subjects = Subject::where('name', 'like', '%' . $name . '%')
                ->where('category_id', 1)
                ->whereHas('years_users', function($q) use ($year_id) {
                    $q->where('teacher_subject_years.year_id', $year_id);
                })
                ->orWhere(function($query) use ($name) {
                    $query->where('name', 'like', '%' . $name . '%')
                          ->where('category_id', '!=', 1);
                })
                ->get();
        }else{
        $subjects = Subject::where('name', 'like', '%' . $name . '%')
        ->get();
    }

    foreach ($subjects as $item) {
        $item->users = Subject::whereHas('years_users', function($query) use ($item) {
            $query->where('subject_id', $item->id);
        })->get();

        $item->users = User::whereIn('id', function($query) use ($item) {
            $query->select('user_id')->from('teacher_subject_years')->where('subject_id', $item->id);
        })->get();
    }

    return response()->json([
        'message' => "These are the items.",
        'subjects' =>$subjects,
    ]);
}else{
    return response()->json([
        'message' => "These are the items.",
        'subjects' =>$subjects,
    ]);
}
}
    //***********************************************************************************************************************\\
    /*search in subjects,category,teachers,unit and lessons.
    in case the user has year_id will see just one subject in his year else he will see all subjects.*/
    public function search(Request $request)
{
    $year_id = $request->query('year_id');
    $name = $request->query('name');
    $categories= [];
    $teachers= [];
    $units= [];
    $lessons= [];
    $subjects= [];
    if($name){

    $categories = Category::where('category', 'like', '%' . $name . '%')
        ->get();

    $teachers = User::where('name', 'like', '%' . $name . '%')
    ->where('role_id',3)
        ->get();

    $units = Unit::where('name', 'like', '%' . $name . '%')
            ->get();

    $lessons = Lesson::where('name', 'like', '%' . $name . '%')
            ->get();

        if($year_id){
            $subjects = Subject::where('name', 'like', '%' . $name . '%')
                ->where('category_id', 1)
                ->whereHas('years_users', function($q) use ($year_id) {
                    $q->where('teacher_subject_years.year_id', $year_id);
                })
                ->orWhere(function($query) use ($name) {
                    $query->where('name', 'like', '%' . $name . '%')
                          ->where('category_id', '!=', 1);
                })
                ->get();
        }else{
        $subjects = Subject::where('name', 'like', '%' . $name . '%')
        ->get();
    }

    foreach ($subjects as $item) {
        $item->users = Subject::whereHas('years_users', function($query) use ($item) {
            $query->where('subject_id', $item->id);
        })->get();

        $item->users = User::whereIn('id', function($query) use ($item) {
            $query->select('user_id')->from('teacher_subject_years')->where('subject_id', $item->id);
        })->get();
    }

    return response()->json([
        'message' => "These are the items.",
        'categories' => $categories,
        'teachers' =>$teachers,
        'units' =>$units,
        'lessons' =>$lessons,
        'subjects' =>$subjects,
    ]);
}else{
    return response()->json([
        'message' => "These are the items.",
        'categories' => $categories,
        'teachers' =>$teachers,
        'units' =>$units,
        'lessons' =>$lessons,
        'subjects' =>$subjects,
    ]);
}
}

//************************************************************************************************************** */
    public function add_subject(Request $request)
{
    $user_id = Auth::id();

    $request->validate([
        'category_id' => 'required',
        'name' => 'required',
        'price' => 'required',
        'description' => 'required',
        'image' => 'required' ,
        'video_id' => 'integer',
        'file_id' => 'integer',
        // 'users_content' => 'required|array',
        // 'users_content.*.user_id' => 'required|integer',
        'years_content.*.year_id' => 'integer',
    ]);
    //  // Check if required fields are missing
    //  if (!$request->filled('category_id') || !$request->filled('users_content') || !$request->filled('users_content.0.user_id')) {
    //     return response()->json(['message' => 'Missing required fields.'], 400);
    // }

    // Check if category exists
    $category = Category::find($request->input('category_id'));
    if (!$category) {
        return response()->json(['message' => 'Category not found.'], 404);
    }

    // Check if image is uploaded
    if (!$request->hasFile('image')) {
        return response()->json(['message' => 'Image file is required.'], 400);
    }

    // Store the image and get the URL
    $imagePath = $request->file('image')->store('subject_images', 'public');
    $imageUrl = Storage::url($imagePath);

    $subject = Subject::create([
        'name' => $request->name,
        'price' => $request->price,
        'description' => $request->description,
        'image_url' => $imageUrl,
        'video_id' => $request->video_id,
        'file_id' => $request->file_id,
        'category_id' => $request->category_id,
    ]);


        if ($request->category_id == 1) { // If the category is educational
            $yearsContent = $request->years_content;
            // $usersContent = $request->users_content;

            // foreach ($usersContent as $user) {
                foreach ($yearsContent as $year) {
                    // $existingUser = User::find($user['user_id']);
                    // if (!$existingUser) {
                    // return response()->json(['message' => 'User not found.'], 404);
                    //     }

                    $existingYear = Year::find($year['year_id']);
                    if (!$existingYear) {
                    return response()->json(['message' => 'Year not found.'], 404);
                        }
                        $subject->years_users()->attach($user_id, ['year_id' => $year['year_id']]);                }
            // }
        }else {
            // foreach ($request->users_content as $user) {
                // $existingUser = User::find($user['user_id']);
                // if (!$existingUser) {
                // return response()->json(['message' => 'User not found.'], 404);
                //     }
                $subject->years_users()->attach($user_id);
                     }
        // }

    return response()->json([
        'message' => 'Subject added successfully.',
        'data' => $subject,
    ]);
}

    //***********************************************************************************************************************\\
    public function edit_subject(Request $request)
    {
        $user_id = Auth::id();
        $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'category_id' => 'integer|exists:categories,id|nullable',
            'name' => 'string|max:255|nullable',
            'price' => 'numeric|nullable',
            'description' => 'string|nullable',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:10240|nullable',
            'video_id' => 'integer|nullable',
            'file_id' => 'integer|nullable',
            // 'users_content' => 'required|array',
            // 'users_content.*.user_id' => 'required|integer|exists:users,id',
            'years_content' => 'array|nullable',
            'years_content.*.year_id' => 'integer|exists:years,id',
        ]);

        $subject = Subject::find($request->subject_id);

        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        $subjectData = $request->only(['name', 'price', 'description', 'category_id', 'video_id', 'file_id']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('subject_images', 'public');
            $imageUrl = Storage::url($imagePath);

            if ($subject->image_url) {
                $oldImagePath = str_replace('/storage', 'public', $subject->image_url);
                Storage::delete($oldImagePath);
            }

            $subjectData['image_url'] = $imageUrl;
        }

        $subject->update($subjectData);

        $subject->years_users()->detach();

        // $usersContent = $request->users_content;
        $yearsContent = $request->years_content ?? [];

        // foreach ($usersContent as $user) {
            if ($request->category_id == 1) {
                foreach ($yearsContent as $year) {
                    $subject->years_users()->attach($user_id , ['year_id' => $year['year_id']]);
                }
            } else {
                $subject->years_users()->attach($user_id);
            }
        // }

        return response()->json([
            'message' => 'Subject updated successfully',
            'data' => $subject,
        ], 200);
    }


    //***********************************************************************************************************************\\
 public function delete_subject($subject_id)
    {
        $user = Auth::user();
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

    //***********************************************************************************************************************\\

}
