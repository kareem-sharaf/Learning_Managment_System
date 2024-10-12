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

use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use App\Services\SubjectService;
use App\Services\UserService;
use App\Services\YearService;

use App\Http\Requests\SubjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TeachersController;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    protected $categoryService;
    protected $subjectService;
    protected $userService;
    protected $yearService;

    public function __construct(
        CategoryService $categoryService,
        SubjectService $subjectService,
        UserService $userService,
        YearService $yearService,
    ) {
        $this->categoryService = $categoryService;
        $this->subjectService = $subjectService;
        $this->userService = $userService;
        $this->yearService = $yearService;
    }

    //**********************************************************************************************\/
    //     /*show all subjects in the category and in case the category is educational we will
    //      need year_id and if we don't have year_id we will show the years.*/
    //      public function show_all_subjects(Request $request)
    // {
    //     $category_id = $request->category_id;
    //     $year_id = $request->year_id;
    //     $subjects = [];
    //     $years = [];

    //     if ($category_id == 1 && $year_id) {
    //         // Get subjects with associated users for the specified year
    //         $subjects = Subject::whereHas('years_users', function($query) use ($year_id) {
    //             $query->where('teacher_subject_years.year_id', $year_id);
    //         })->where('exist', true)->get();
    //     } elseif ($category_id == 1 && !$year_id) {
    //         // No year_id selected, return available years
    //         $years = Year::all();
    //     } else {
    //         // Get subjects based on category
    //         $subjects = Subject::where('category_id', $category_id)->where('exist', true)->get();
    //     }

    //     // Fetch users for each subject and attach files if available
    //     foreach ($subjects as $subject) {
    //         // Get users associated with the subject (based on the `teacher_subject_years` relationship)
    //         $subjectUsers = User::whereIn('id', function($query) use ($subject) {
    //             $query->select('user_id')
    //                   ->from('teacher_subject_years')
    //                   ->where('subject_id', $subject->id);
    //         })->get();

    //         // Attach users and files to the subject
    //         $subject->users = $subjectUsers;
    //         $subject->files = $subject->files;  // Assuming the subject model has a `files` relationship

    //         // Attach subject image URL (ensure the image column holds a valid URL)
    //         $subject->image_url = $subject->image;

    //         // Attach user profile images if they have them
    //         foreach ($subject->users as $user) {
    //             $user->profile_image_url = $user->profile_image ? url($user->profile_image) : null;
    //         }
    //     }

    //     // Return response with subjects and years
    //     return response()->json([
    //         'message' => 'This is all subjects in the category.',
    //         'data' => $subjects,
    //         'years' => $years,
    //     ]);
    // }


    //*********************************************************************************************** */
    /*show all subjects in the category and in case the category is educational we will
     need year_id and if we don't have year_id we will show the years.*/
    public function show_all_subjects($category_id, $year_id = null)
    {
        if (!$this->categoryService->validateCategoryYear($category_id, $year_id)) {
            return response()->json([
                'message' => 'Please select a year!',
                'years' => $this->yearService->index(),
            ], 400);
        }

        $subjects = $this->subjectService->getSubjects($category_id, $year_id);
        if ($subjects->isEmpty()) {
            return response()->json(['message' => 'No subjects found in this category!'], 404);
        }

        $subjects_with_users = $this->userService->attachUsersToSubjects($subjects);

        return response()->json([
            'message' => 'Subjects with users inside the category:',
            'subjects' => $subjects_with_users,
        ], 200);
    }

    //******************************************************************************************* */
    //  show specific subject
    public function showOne($subject_id)
    {
        $subject = Subject::where('id', $subject_id)->first();
        if (!$subject) {
            return response()->json(
                [
                    'message' => 'the subject does not exist ! .',
                ]
            );
        }
        $subjects = collect([$subject]);
        return response()->json(
            [
                'message' => 'this is the subject .',
                'subject'=>$this->userService->attachUsersToSubjects($subjects)
            ]
        );
    }
    //************************************************************************************************************** */
    public function add_subject(SubjectRequest $request)
    {
        $user_id = Auth::id();

        // Validate request
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
            'year_id' => 'nullable|integer|exists:years,id',
        ]);

        // Handle image upload and store it in 'subject_images'
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('subject_images'), $imageName);
        $imageUrl = url('subject_images/' . $imageName);

        // Create the subject
        $subject = new Subject([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imageUrl,
            'category_id' => $request->category_id,
        ]);

        if ($subject->save()) {
            // Handle video upload
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

            // Handle file upload
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

            // Handle year association if category is educational
            if ($request->category_id == 1) {
                if ($request->has('year_id')) {
                    $yearId = $request->year_id;
                    $existingYear = Year::find($yearId);
                    if (!$existingYear) {
                        return response()->json(['message' => 'Year not found.'], 404);
                    }
                    $subject->years_users()->attach($user_id, ['year_id' => $yearId]);
                } else {
                    return response()->json(['message' => 'You need to specify a year.'], 404);
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

        // Validate request
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
        ]);

        // Find subject
        $subject = Subject::find($request->subject_id);

        if (!$subject) {
            return response()->json(['message' => 'Subject not found.'], 404);
        }

        $subjectData = $request->only(['name', 'price', 'description', 'category_id']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($subject->image) {
                $oldImagePath = public_path('subject_images/' . basename($subject->image));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Store new image
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('subject_images'), $imageName);
            $subject->image = url('subject_images/' . $imageName);
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $video = Video::find($subject->video_id);

            if ($video) {
                // Delete old video
                $oldVideoPath = public_path('videos/' . basename($video->video));
                if (file_exists($oldVideoPath)) {
                    unlink($oldVideoPath);
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

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = File::find($subject->file_id);

            if ($file) {
                // Delete old file
                $oldFilePath = public_path('files/' . basename($file->file));
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
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

        // Update subject data
        $subject->update($subjectData);

        return response()->json([
            'message' => 'Subject updated successfully',
            'data' => $subject,
        ], 200);
    }



    //***********************************************************************************************************************\\
    public function delete_subject(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $role_id = $user->role_id;
        $subject_id = $request->subject_id;
        $subject = Subject::find($subject_id);
        $teacher_subject = TeacherSubjectYear::where('user_id', $user_id)
            ->where('subject_id', $subject_id)->first();
        if (($teacher_subject && $role_id == 3) || $role_id == 2 || $role_id == 1) {
            if ($subject) {
                $subject->update(['exist' => false]);

                Unit::where('subject_id', $subject->id)
                    ->update(['exist' => false]);

                Lesson::whereIn('unit_id', function ($query) use ($subject) {
                    $query->select('id')
                        ->from('units')
                        ->where('subject_id', $subject->id);
                })->update(['exist' => false]);

                return response()->json(['message' => 'Subject and related items have been deleted successfuly.']);
            } else {
                return response()->json(['message' => 'Subject not found.'], 404);
            }
        } else {
            return response()->json(['message' => 'you cannot delete this subject.'], 403);
        }
    }

    //***********************************************************************************************************************\\


}
