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
use App\Models\Video;
use App\Models\File;

 use App\Http\Responses\ApiSuccessResponse;
 use App\Http\Responses\ApiErrorResponse;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\Storage;
//  use Illuminate\Support\Facades\File;


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
        $user = auth()->user();  // Use auth() helper properly
        $user_id = $user->id;

        $year_id = $request->query('year_id');
        $categories = Category::all();

        $categoriesWithSubjects = [];

        foreach ($categories as $category) {
            $categoryData = [
                'category' => $category,
                'subjects' => []
            ];

            if ($category->id == 1 && $year_id) {
                $subjects = Subject::where('category_id', $category->id)
                    ->whereHas('years_users', function($query) use ($year_id) {
                        $query->where('teacher_subject_years.year_id', $year_id);
                    })
                    ->get();
            } elseif ($category->id == 1 && !$year_id) {
                $categoryData['years'] = Year::all();
                $subjects = collect(); // No subjects to add in this case
            } else {
                $subjects = Subject::where('category_id', $category->id)->get();
            }

            foreach ($subjects as $subject) {
                if (!$subject->exist) {
                    $subscription = Subscription::where('subject_id', $subject->id)
                        ->where('user_id', $user_id)
                        ->first();

                    if (!$subscription) {
                        continue;
                    }
                }

                // Get users associated with the subject and files
                $subjectUsers = User::whereIn('id', function($query) use ($subject) {
                    $query->select('user_id')
                          ->from('teacher_subject_years')
                          ->where('subject_id', $subject->id);
                })->get();

                $subject->users = $subjectUsers;
                $subject->files = $subject->files;  // Attach files to the subject

                $categoryData['subjects'][] = $subject; // Add the subject to the category
            }

            // Only add category data if subjects are present or years data exists
            if (!empty($categoryData['subjects']) || isset($categoryData['years'])) {
                $categoriesWithSubjects[] = $categoryData;
            }
        }

        $message = "This is all the data";
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
                ->where('exist',true)
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
        ->where('exist',true)
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
    $categories = [];
    $teachers = [];
    $units = [];
    $lessons = [];
    $subjects = [];

    if ($name) {
        $categories = Category::where('category', 'like', '%' . $name . '%')
        ->where('exist', true)->get();

        $teachers = User::where('name', 'like', '%' . $name . '%')
            ->where('role_id', 3)
            ->where('email', '!=', 'deleted_user@example.com')
            ->get();

        $units = Unit::where('name', 'like', '%' . $name . '%')
        ->where('exist', true) ->get();

        $lessons = Lesson::where('name', 'like', '%' . $name . '%')
        ->where('exist', true) ->get();

        if ($year_id) {
            $subjects = Subject::where('name', 'like', '%' . $name . '%')
                ->where('exist', true)
                ->where('category_id', 1)
                ->whereHas('years_users', function ($q) use ($year_id) {
                    $q->where('teacher_subject_years.year_id', $year_id);
                })
                ->orWhere(function ($query) use ($name) {
                    $query->where('name', 'like', '%' . $name . '%')
                          ->where('category_id', '!=', 1)
                          ->where('exist', true);
                })
                ->get();
        } else {
            $subjects = Subject::where('name', 'like', '%' . $name . '%')
                ->where('exist', true)  
                ->get();
        }

        foreach ($subjects as $item) {
            $item->users = User::whereIn('id', function ($query) use ($item) {
                $query->select('user_id')
                      ->from('teacher_subject_years')
                      ->where('subject_id', $item->id);
            })->get();
        }

        return response()->json([
            'message' => "These are the items.",
            'categories' => $categories,
            'teachers' => $teachers,
            'units' => $units,
            'lessons' => $lessons,
            'subjects' => $subjects,
        ]);
    } else {
        return response()->json([
            'message' => "These are the items.",
            'categories' => $categories,
            'teachers' => $teachers,
            'units' => $units,
            'lessons' => $lessons,
            'subjects' => $subjects,
        ]);
    }
}


//************************************************************************************************************** */
    public function add_subject(Request $request)
{
    $user_id = Auth::id();

    $request->validate([
        'category_id' => 'required|integer|exists:categories,id',
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'description' => 'required|string',
        'image' => 'required|image|max:10240',
        'video' => 'nullable|mimes:mp4,mov,avi,flv|max:204800',
        'video_name' => 'nullable|string|max:255',
        'file_name' => 'nullable|string|max:255',
        'file' => 'nullable|file|max:20480',
        'years_content.*.year_id' => 'nullable|integer|exists:years,id',
    ]);

    $imagePath = $request->file('image')->store('subject_images', 'public');
    $imageUrl = Storage::url($imagePath);

    $subject = new Subject([
        'name' => $request->name,
        'price' => $request->price,
        'description' => $request->description,
        'image_url' => $imageUrl,
        'category_id' => $request->category_id,
    ]);

    if ($subject->save()) {
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
            $video = new Video();
            $video->video = Storage::url($videoPath);
            $video->name = $request->video_name;
            $video->subject_id = $subject->id;
            $video->save();

            $subject->video_id = $video->id;
            $subject->save();
        }
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('files', 'public');
            $file = new File();
            $file->file = Storage::url($filePath);
            $file->name = $request->file_name;
            $file->subject_id = $subject->id;
            $file->save();

            $subject->file_id = $file->id;
            $subject->save();
        }

        if ($request->category_id == 1) { // If the category is educational
            if ($request->has('years_content')) {
                $yearsContent = $request->years_content;
                foreach ($yearsContent as $year) {
                    $existingYear = Year::find($year['year_id']);
                    if (!$existingYear) {
                        return response()->json(['message' => 'Year not found.'], 404);
                    }
                    $subject->years_users()->attach($user_id, ['year_id' => $year['year_id']]);
                }
            }else{
                return response()->json(['message' => 'you need to year.'], 404);
            }
        } else {
            $subject->years_users()->attach($user_id);
        }

        $subject->load('videos');
        $subject->load('files');

        return response()->json([
            'message' => 'Subject added successfully.',
            'data' => $subject,
        ]);
    }

    return response()->json(['message' => 'Failed to add subject.'], 500);
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
        'video' => 'nullable|mimes:mp4,mov,avi,flv|max:204800',
        'video_name' => 'nullable|string|max:255',
        'file.*' => 'nullable|file|max:204800',
        'file_name.*' => 'nullable|string|max:255',
        'years_content' => 'array|nullable',
        'years_content.*.year_id' => 'integer|exists:years,id',
    ]);

    $subject_id = $request->subject_id;

    $SubjectTeacher = TeacherSubjectYear::where('user_id', $user_id)
                                        ->where('subject_id', $subject_id)
                                        ->first();
    if (!$SubjectTeacher) {
        return response()->json([
            'message' => 'you can not edit this subject.',
        ], 404);
    }

    $subject = Subject::find($subject_id);
    if (!$subject) {
        return response()->json([
            'message' => 'Subject not found.',
        ], 404);
    }

    $subjectData = $request->only(['name', 'price', 'description', 'category_id', 'video_id', 'file_id']);

    if ($request->hasFile('image')) {
        // Delete old image
        if ($subject->image_url) {
            $oldImagePath = str_replace('/storage', 'public', $subject->image_url);
            if (Storage::exists($oldImagePath)) {
                Storage::delete($oldImagePath);
            }
        }

        // Store new image
        $imagePath = $request->file('image')->store('subject_images', 'public');
        $subject->image_url = Storage::url($imagePath);
    }


    if ($request->hasFile('video')) {
        $video_id = $subject->video_id;
        $video = Video::find($video_id);
        if ($video) {
            // Delete old video
            $oldVideoPath = str_replace('/storage', 'public', $video->video);
            if (Storage::exists($oldVideoPath)) {
                Storage::delete($oldVideoPath);
            }
        } else {
            // Create new video instance if it doesn't exist
            $video = new Video();
            $video->subject_id = $subject->id;
        }

        // Store new video
        $videoPath = $request->file('video')->store('videos', 'public');
        $video->video = Storage::url($videoPath);

        if ($request->filled('video_name')) {
            $video->name = $request->video_name;
        }

        $video->save();
        $subject->video_id = $video->id;
    }

    if ($request->hasFile('file')) {
        $file_id = $subject->file_id;
        $file = File::find($file_id);
        if ($file) {
            // Delete old file
            $oldfilePath = str_replace('/storage', 'public', $file->file);
            if (Storage::exists($oldfilePath)) {
                Storage::delete($oldfilePath);
            }
        } else {
            // Create new file instance if it doesn't exist
            $file = new File();
            $file->subject_id = $subject->id;
        }

        // Store new file
        $filePath = $request->file('file')->store('files', 'public');
        $file->file = Storage::url($filePath);

        if ($request->filled('file_name')) {
            $file->name = $request->file_name;
        }

        $file->save();
        $subject->file_id = $file->id;
    }

         $subject->update($subjectData);

        $subject->years_users()->detach();

        $yearsContent = $request->years_content ?? [];

        if ($request->category_id == 1) {
            foreach ($yearsContent as $year) {
                $subject->years_users()->attach($user_id, ['year_id' => $year['year_id']]);
            }
        } else {
            $subject->years_users()->attach($user_id);
        }

        return response()->json([
            'message' => 'Subject updated successfully',
            'data' => $subject,
        ], 200);
}


    //***********************************************************************************************************************\\
    public function delete_subject(Request $request )
    {
        $user = Auth::user();
        $user_id=$user->id;
        $role_id=$user->role_id;
        $subject_id = $request->subject_id;
        $subject = Subject::find($subject_id);
        $teacher_subject=TeacherSubjectYear::where('user_id',$user_id)
        ->where('subject_id',$subject_id)->first();
        if(($teacher_subject && $role_id==3) || $role_id==2 || $role_id==1){
            if ($subject) {
                $subject->update(['exist' => false]);

                Unit::where('subject_id', $subject->id)
                    ->update(['exist' => false]);

                Lesson::whereIn('unit_id', function($query) use ($subject) {
                    $query->select('id')
                        ->from('units')
                        ->where('subject_id', $subject->id);
                })->update(['exist' => false]);

                return response()->json(['message' => 'Subject and related items have been deleted successfuly.']);
            } else {
                return response()->json(['message' => 'Subject not found.'], 404);
            }
        }else{
            return response()->json(['message' => 'you cannot delete this subject.'], 403);
        }
    }

    //***********************************************************************************************************************\\


}
