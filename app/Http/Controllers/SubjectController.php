<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\TeacherSubjectYear;
use Illuminate\Support\Facades\Auth;
use App\Services\CategoryService;
use App\Services\SubjectService;
use App\Services\UserService;
use App\Services\UnitService;
use App\Services\LessonService;
use App\Services\ImageService;
use App\Services\VideoService;
use App\Services\FileService;
use App\Services\YearService;
use App\Http\Requests\SubjectRequest;


class SubjectController extends Controller
{
    protected $categoryService;
    protected $userService;
    protected $unitService;
    protected $lessonService;
    protected $subjectService;
    protected $imageService;
    protected $videoService;
    protected $fileService;
    protected $yearService;

    public function __construct(
        CategoryService $categoryService,
        UserService $userService,
        UnitService $unitService,
        LessonService $lessonService,
        SubjectService $subjectService,
        ImageService $imageService,
        VideoService $videoService,
        FileService $fileService,
        YearService $yearService
    ) {
        $this->categoryService = $categoryService;
        $this->userService = $userService;
        $this->unitService = $unitService;
        $this->lessonService = $lessonService;
        $this->subjectService = $subjectService;
        $this->imageService = $imageService;
        $this->videoService = $videoService;
        $this->fileService = $fileService;
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
        // if the cat = 1 and year doesn't exist the response is years .
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
            return response()->json(['message' => 'the subject does not exist ! .']);
        }
        $subjects = collect([$subject]);
        return response()->json(
            [
                'message' => 'this is the subject .',
                'subject' => $this->userService->attachUsersToSubjects($subjects)
            ]
        );
    }
    //************************************************************************************************************** */
    public function add_subject(SubjectRequest $request)
    {
        $user_id = Auth::id();
        $data = $request->validated();


        $data['image'] = $this->imageService->uploadImage($request->file('image'), 'subjects_images');
        $subject = Subject::create($data);


        // Handle video upload
        if ($request->hasFile('video')) {
            $this->videoService->saveVideo($request->file('video'), $subject, $request->video_name);
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $this->fileService->saveFile($request->file('file'), $subject, $request->file_name);
        }

        // Handle year association
        if ($request->category_id == 1) {
            $this->subjectService->associateYear($subject, $request->year_id, $user_id);
        } else {
            $this->subjectService->associateUser($subject, $user_id);
        }

        return response()->json([
            'message' => 'Subject added successfully.',
            'data' => $subject,
        ]);
    }


    //***********************************************************************************************************************\\
    public function edit_subject(SubjectRequest $request, $subject_id)
    {
        $data = $request->validated();
        $subject = $this->subjectService->getSubject($subject_id);
        if (!$subject) {
            return response()->json(
                [
                    'message' => 'subject does not exist!.',
                ],
                404
            );
        }
        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $this->imageService->replaceImage($request->file('image'), $subject->image, 'subjects_images');
        }
        // Handle video upload
        if ($request->hasFile('video')) {
            $this->videoService->replaceVideo($request->file('video'), $subject, $request->video_name);
        }
        // Handle file upload
        if ($request->hasFile('file')) {
            $this->fileService->replaceFile($request->file('file'), $subject, $request->file_name);
        }
        // Update subject data
        $subject->update($data);
        return response()->json(
            [
                'message' => 'Subject updated successfully',
                'data' => $subject,
            ],
            200
        );
    }



    //***********************************************************************************************************************\\
    public function delete_subject($subject_id)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $teacher_subject = TeacherSubjectYear::where('user_id', $user_id)
            ->where('subject_id', $subject_id)->first();

        if (!$teacher_subject) {
            return response()->json(['message' => 'You cannot delete this subject.'], 403);
        }


        $this->subjectService->deleteSubjectWithRelations($subject_id);

        return response()->json(['message' => 'Subject and related items have been deleted successfuly.']);
    }

    //***********************************************************************************************************************\\


}
