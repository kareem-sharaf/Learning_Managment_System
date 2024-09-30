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


class LessonController extends Controller
{
    public function add_lesson(Request $request)
    {
        $user=Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required',
            // 'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'video' => 'nullable|mimes:mp4,mov,avi,flv|max:204800',
            'video_name' => 'nullable|string|max:255',
            'file_name' => 'string|max:255',
            'file' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:20480',
        ]);


        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('lesson_images'), $imageName);
        $imageUrl = url('lesson_images/' . $imageName);

        $lesson = new Lesson([
            'name' => $request->name,
            'description' => $request->description,
            'unit_id' => $request->unit_id,
            'image' => $imageUrl,
            'teacher_id' => Auth::id(),
        ]);

        if ($lesson->save()) {

            // تخزين الفيديو إذا تم رفعه
            if ($request->hasFile('video')) {
                $videoPath = $request->file('video')->store('videos', 'public');
                $video = new Video();
                $video->video = Storage::url($videoPath);
                $video->name = $request->video_name;
                $video->lesson_id = $lesson->id;
                $video->save();

                $lesson->video_id = $video->id;
                $lesson->save();
            }

            // تخزين الملفات إذا تم رفعها
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('files', 'public');
                $file = new File();
                $file->file = Storage::url($filePath); // تأكد من استخدام الاسم الصحيح للعمود
                $file->name = $request->file_name;
                $file->lesson_id = $lesson->id;
                $file->save();

                $lesson->file_id = $file->id;
                $lesson->save();
            }

            // تحميل الفيديوهات والملفات المرتبطة بالدرس
            $lesson->load('videos', 'files');
            return response()->json([
                'message' => 'Lesson created successfully',
                'data' => $lesson,
                'status' => 200,
            ]);
        } else {
            Storage::delete($imagePath);

            return response()->json([
                'message' => 'Lesson creation failed',
                'status' => 400,
            ]);
        }
    }

    public function update_lesson(Request $request)
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
        $user=Auth::user();
        $user_id=$user->id;
        $lesson_id=$request->lesson_id;
        $lesson = Lesson::find($lesson_id);
        $unit_id=$lesson->unit_id;
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

    public function getLessonsByUnitId(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|integer|exists:units,id',
        ]);

        $unitId = $request->unit_id;

        $lessons = Lesson::where('unit_id', $unitId)->where('exist',true)->get();

        if ($lessons) {
            return response()->json([
                'message' => 'Lessons retrieved successfully',
                'data' => $lessons,
                'tatus' => 200,
            ]);
        } else {
            return response()->json([
                'message' => 'No lessons found for this unit',
                'status' => 404,
            ]);
        }
    }

    public function getLessonById(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $role_id = $user->role_id;

        $request->validate([
            'id' => 'required|integer|exists:lessons,id',
        ]);

        $lessonId = $request->id;

        $lesson = Lesson::with(['files', 'videos'])->find($lessonId);
        $unit_id=$lesson->unit_id;
        $unit = Unit::find($unit_id);
        $subject_id=$unit->subject_id;

        if ($lesson) {
            $lesson->videos->transform(function ($video) {
                $video->video_url = Storage::url($video->video);
                return $video;
            });

            $lesson->files->transform(function ($file) {
                $file->file_url = Storage::url($file->content);
                return $file;
            });
            if ($role_id == 4) {
                $isSubscription = Subscription::where('user_id', $user_id)
                    ->where('subject_id', $subject_id)
                    ->exists();

                return response()->json([
                    'status' => $isSubscription,
                    'message' => 'This is all lesson',
                    'data' => $lesson
                ]);
            } elseif ($role_id == 3) {
                $isOwner = TeacherSubjectYear::where('user_id', $user_id)
                    ->where('subject_id', $subject_id)
                    ->exists();

                return response()->json([
                    'status' => $isOwner,
                    'message' => 'This is all lesson',
                    'data' => $lesson
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'This is all lesson.',
                    'data' => $lesson
                ]);
            }

    }
}
}
