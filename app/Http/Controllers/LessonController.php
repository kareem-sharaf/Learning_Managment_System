<?php

namespace App\Http\Controllers;

use Alaouy\Youtube\Facades\Youtube;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\File;
use App\Models\Lesson;
use App\Models\Video;
use App\Models\File;

use Illuminate\Support\Facades\Auth;


class LessonController extends Controller
{

    public function add_lesson(Request $request)
{

    $request->validate([
        'name' => 'required|string|max:255',
        'unit_id' => 'required',
        'price' => 'required|numeric|min:0',
        'description' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        'video' => 'nullable|mimes:mp4,mov,avi,flv|max:204800',
        'video_name' => 'nullable|string|max:255',
        'file_name' => 'required|string|max:255',
        'file' => 'required|file|max:20480',
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
            $file->content = Storage::url($filePath); // تأكد من استخدام الاسم الصحيح للعمود
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
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id|numeric',
            'name' => 'string|max:255',
            'unit_id' => 'exists:units,id',
            'price' => 'numeric|min:0',
            'description' => 'string|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'video' => 'mimes:mp4,mov,avi,flv|max:204800',
            'video_name' => 'string|max:255',
        ]);

        $lesson = Lesson::findOrFail($request->lesson_id);

        // Check if the current user is the owner of the lesson or an admin
        if (Auth::id() !== $lesson->teacher_id && Auth::user()->role_id !== '2') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $isDirty = false;

        if ($request->filled('name') && $lesson->name !== $request->name) {
            $lesson->name = $request->name;
            $isDirty = true;
        }

        if ($request->filled('price') && $lesson->price !== $request->price) {
            $lesson->price = $request->price;
            $isDirty = true;
        }

        if ($request->filled('description') && $lesson->description !== $request->description) {
            $lesson->description = $request->description;
            $isDirty = true;
        }

        if ($request->filled('unit_id') && $lesson->unit_id !== $request->unit_id) {
            $lesson->unit_id = $request->unit_id;
            $isDirty = true;
        }

        if ($request->hasFile('image')) {
            if ($lesson->image) {
                $oldImagePath = str_replace('/storage', 'public', $lesson->image);
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
            }

            $imagePath = $request->image->store('lessons_images', 'public');
            $lesson->image = Storage::url($imagePath);
            $isDirty = true;
        }

        if ($request->hasFile('video')) {
            $video = $lesson->video;

            if ($video) {
                $oldVideoPath = str_replace('/storage', 'public', $video->video);
                if (Storage::exists($oldVideoPath)) {
                    Storage::delete($oldVideoPath);
                }
            } else {
                $video = new Video();
                $video->lesson_id = $lesson->id;
            }

            $videoPath = $request->video->store('videos', 'public');
            $video->video = Storage::url($videoPath);

            if ($request->filled('video_name')) {
                $video->name = $request->video_name;
            }

            $video->save();
            $lesson->video_id = $video->id;
            $isDirty = true;
        }

        if ($isDirty) {
            $lesson->save();
            return response()->json([
                'message' => 'Lesson updated successfully',
                'data' => $lesson,
                'status' => 200,
            ]);
        } else {
            return response()->json(['error' => 'Nothing to update'], 400);
        }
    }

    public function delete_lesson(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id|numeric',
        ]);

        $lesson = Lesson::findOrFail($request->lesson_id);

        // Check if the current user is the owner of the lesson or an admin
        if (Auth::id() !== $lesson->teacher_id && Auth::user()->role_id !== '2') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($lesson->video) {
            $videoPath = str_replace('/storage', 'public', $lesson->video->video);
            if (Storage::exists($videoPath)) {
                Storage::delete($videoPath);
            }
            $lesson->video->delete();
        }

        if ($lesson->image) {
            $imagePath = str_replace('/storage', 'public', $lesson->image);
            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
            }
        }

        if ($lesson->delete()) {
            return response()->json([
                'message' => 'Lesson and associated files deleted successfully',
                'status' => 200,
            ]);
        } else {
            return response()->json([
                'message' => 'Lesson not deleted',
                'status' => 400,
            ]);
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
