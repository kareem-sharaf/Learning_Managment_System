<?php

namespace App\Http\Controllers;

use Alaouy\Youtube\Facades\Youtube;
use App\Models\File;
use App\Models\Unit;
use App\Models\TeacherSubjectYear;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\File;
use App\Models\Lesson;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;

use App\Services\UserService;
use App\Services\UnitService;
use App\Services\LessonService;
use App\Services\VideoService;
use App\Services\FileService;
use App\Services\SubjectService;
use App\Services\ImageService;


class LessonController extends Controller
{
    protected $userService;
    protected $unitService;
    protected $lessonService;
    protected $videoService;
    protected $fileService;
    protected $subjectService;
    protected $imageService;

    public function __construct(
        UserService $userService,
        UnitService $unitService,
        LessonService $lessonService,
        VideoService $videoService,
        FileService $fileService,
        SubjectService $subjectService,
        ImageService $imageService,

    ) {
        $this->userService = $userService;
        $this->unitService = $unitService;
        $this->lessonService = $lessonService;
        $this->videoService = $videoService;
        $this->fileService = $fileService;
        $this->subjectService = $subjectService;
        $this->imageService = $imageService;
    }
    //*******************************************************************************************
    public function show_all_lessons($unit_id)
    {
        $unit = $this->unitService->getUnit($unit_id);
        if (!$unit) {
            return response()->json(['error' => 'Unit does not exist!.'], 404);
        }
        $lessons = $this->lessonService->getLessons($unit_id);

        if ($lessons->isEmpty()) {
            return response()->json(['message' => 'No lessons found in this unit!'], 404);
        }


        return response()->json([
            'message' => 'This is all units',
            'data' => $lessons
        ]);
    }
    //*******************************************************************************************

    public function show_lesson($lesson_id)
    {
        $lesson = $this->lessonService->getLesson($lesson_id);
        if (!$lesson) {
            return response()->json(['message' => 'lesson does not exist!'], 404);
        }

        return response()->json([
            'message' => 'This is the lesson',
            'data' => $lesson
        ]);
    }
    //*******************************************************************************************
    public function add(Request $request)
    {
        $user_id = Auth::id();
        if (!$this->lessonService->accessLesson($user_id, $request->subject_id)) {
            return response()->json(['message' => 'You cannot add this unit.'], 403);
        }
        $data = $request->validated();


        $data['image'] = $this->imageService->uploadImage($request->file('image'), 'lessons_images');
        $lesson = Lesson::create($data);


        // Handle video upload
        if ($request->hasFile('video')) {
            $this->videoService->saveVideo($request->file('video'), $lesson, $request->video_name);
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $this->fileService->saveFile($request->file('file'), $lesson, $request->file_name);
        }

        return response()->json([
            'message' => 'lesson added successfully',
            'data' => $lesson,
        ]);
    }
//*******************************************************************************************
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'lesson_id' => 'required|exists:lessons,id|numeric',
            'name' => 'nullable|string|max:255',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'video' => 'nullable|mimes:mp4,mov,avi,flv|max:204800',
            'video_name' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:20480',
            'file_name' => 'nullable|string|max:255',
        ]);

        $lesson = Lesson::findOrFail($request->lesson_id);

        // تحقق من صلاحيات المستخدم
        if (Auth::id() !== $lesson->teacher_id && Auth::user()->role_id !== 2) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // تحديث الصورة إذا تم رفع صورة جديدة
        if ($request->hasFile('image')) {
            if ($lesson->image) {
                // حذف الصورة القديمة إذا كانت موجودة
                $oldImagePath = str_replace(url(''), '', $lesson->image);
                if (file_exists(public_path($oldImagePath))) {
                    unlink(public_path($oldImagePath));
                }
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('lesson_images'), $imageName);
            $lesson->image = url('lesson_images/' . $imageName);
        }

        // تحديث الفيديو إذا تم رفع فيديو جديد
        if ($request->hasFile('video')) {
            if ($lesson->video_id) {
                $video = Video::find($lesson->video_id);
                if ($video) {
                    // حذف الفيديو القديم إذا كان موجودًا
                    $oldVideoPath = str_replace(url(''), '', $video->video);
                    if (file_exists(public_path($oldVideoPath))) {
                        unlink(public_path($oldVideoPath));
                    }
                    $video->delete(); // حذف سجل الفيديو القديم
                }
            }

            $videoPath = $request->file('video')->store('videos', 'public');
            $video = new Video();
            $video->video = Storage::url($videoPath);
            $video->name = $request->video_name ?? 'No name'; // تأكد من أن الفيديو يحتوي على اسم
            $video->lesson_id = $lesson->id;
            $video->save();

            $lesson->video_id = $video->id;
        }

        // تحديث الملف إذا تم رفع ملف جديد
        if ($request->hasFile('file')) {
            if ($lesson->file_id) {
                $file = File::find($lesson->file_id);
                if ($file) {
                    // حذف الملف القديم إذا كان موجودًا
                    $oldFilePath = str_replace(url(''), '', $file->file);
                    if (file_exists(public_path($oldFilePath))) {
                        unlink(public_path($oldFilePath));
                    }
                    $file->delete(); // حذف سجل الملف القديم
                }
            }

            $filePath = $request->file('file')->store('files', 'public');
            $file = new File();
            $file->file = Storage::url($filePath);
            $file->name = $request->file_name ?? 'No name'; // تأكد من أن الملف يحتوي على اسم
            $file->lesson_id = $lesson->id;
            $file->save();

            $lesson->file_id = $file->id;
        }

        // تحديث باقي الحقول إذا كانت موجودة في الطلب
        if ($request->filled('name')) {
            $lesson->name = $request->name;
        }
        if ($request->filled('description')) {
            $lesson->description = $request->description;
        }
        if ($request->filled('unit_id')) {
            $lesson->unit_id = $request->unit_id;
        }

        $lesson->save();

        // تحميل الفيديوهات والملفات المرتبطة بالدرس
        $lesson->load('videos', 'files');

        return response()->json([
            'message' => 'Lesson updated successfully',
            'data' => $lesson,
            'status' => 200,
        ]);
    }


    public function delete_lesson(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $lesson_id = $request->lesson_id;
        $lesson = Lesson::find($lesson_id);
        $unit_id = $lesson->unit_id;
        $unit = Unit::find($unit_id);
        $subject_id = $unit->subject_id;
        $SubjectTeacher = TeacherSubjectYear::where('user_id', $user_id)
            ->where('subject_id', $subject_id)
            ->first();

        if (!$SubjectTeacher) {
            return response()->json([
                'message' => 'you cannot delete this unit.',
            ], 404);
        }

        if ($lesson) {
            $lesson->update(['exist' => false]);

            return response()->json(['message' => 'Lesson has been deleted successfuly.']);
        } else {
            return response()->json(['message' => 'Lesson not found.'], 404);
        }
    }
}
