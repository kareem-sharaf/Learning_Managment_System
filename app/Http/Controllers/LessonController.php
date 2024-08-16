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
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'video' => 'nullable|mimes:mp4,mov,avi,flv|max:204800',
            'video_name' => 'nullable|string|max:255',
            'file_name' => 'string|max:255',
            'file' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:20480',
        ]);


        $imagePath = $request->file('image')->store('lessons_images', 'public');


        $lesson = new Lesson();
        $lesson->name = $request->name;
        $lesson->price = $request->price;
        $lesson->description = $request->description;
        $lesson->unit_id = $request->unit_id;
        $lesson->image = Storage::url($imagePath);
        $lesson->teacher_id = Auth::id();

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
        $user=Auth::user();

        $request->validate([
            'lesson_id' => 'required|exists:lessons,id|numeric',
            'name' => 'nullable|string|max:255',
            'unit_id' => 'nullable|exists:units,id',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'video.*' => 'nullable|mimes:mp4,mov,avi,flv|max:204800',
            'video_name.*' => 'nullable|string|max:255',
            'file.*' => 'nullable|file|max:204800',
            'file_name.*' => 'nullable|string|max:255',
        ]);

        $lesson = Lesson::findOrFail($request->lesson_id);

        if (Auth::id() !== $lesson->teacher_id && Auth::user()->role_id !== '2') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->hasFile('image')) {
            if ($lesson->image) {
                $oldImagePath = str_replace('/storage', 'public', $lesson->image);
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
            }

            $imagePath = $request->file('image')->store('lessons_images', 'public');
            $lesson->image = Storage::url($imagePath);
        }

        if ($request->hasFile('video')) {
            $video_id = $lesson->video_id;
            $video = Video::find($video_id);
            if ($video) {
                // Delete old video
                $oldVideoPath = str_replace('/storage', 'public', $video->video);
                if (Storage::exists($oldVideoPath)) {
                    Storage::delete($oldVideoPath);
                }
            } else {
                $video = new Video();
                $video->lesson_id = $lesson->id;
            }

            $videoPath = $request->file('video')->store('videos', 'public');
            $video->video = Storage::url($videoPath);

            if ($request->filled('video_name')) {
                $video->name = $request->video_name;
            }

            $video->save();
            $lesson->video_id = $video->id;
        }








        if ($request->hasFile('file')) {
            $file_id = $lesson->file_id;
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
                $file->lesson_id = $lesson->id;
            }

            // Store new file
            $filePath = $request->file('file')->store('files', 'public');
            $file->file = Storage::url($filePath);

            if ($request->filled('file_name')) {
                $file->name = $request->file_name;
            }

            $file->save();
            $lesson->file_id = $file->id;
        }

        // تحديث معلومات الدرس
        if ($request->filled('name')) {
            $lesson->name = $request->name;
        }
        if ($request->filled('price')) {
            $lesson->price = $request->price;
        }
        if ($request->filled('description')) {
            $lesson->description = $request->description;
        }
        if ($request->filled('unit_id')) {
            $lesson->unit_id = $request->unit_id;
        }

        $lesson->save();
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

        $lessons = Lesson::where('unit_id', $unitId)->get();

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
        $request->validate([
            'id' => 'required|integer|exists:lessons,id',
        ]);

        $lessonId = $request->id;

        $lesson = Lesson::with(['files', 'videos'])->find($lessonId);

        if ($lesson) {
            $lesson->videos->transform(function ($video) {
                $video->video_url = Storage::url($video->video);
                return $video;
            });

            $lesson->files->transform(function ($file) {
                $file->file_url = Storage::url($file->content);
                return $file;
            });

            return response()->json([
                'message' => 'Lesson retrieved successfully',
                'data' => $lesson,
                'status' => 200,
            ]);
        } else {
            return response()->json([
                'message' => 'Lesson not found',
                'status' => 404,
            ]);
        }
    }
}
