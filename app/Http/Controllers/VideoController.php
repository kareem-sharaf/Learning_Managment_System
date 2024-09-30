<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VideoController extends Controller
{
    public function store(Request $request)
{
    $sender = Auth::user();
    $sender_role_id = $sender->role_id;

    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'video' => 'required|file|mimes:mp4,mov,avi,flv|max:204800',
        'subject_id' => 'nullable|integer|exists:subjects,id',
        'unit_id' => 'nullable|integer|exists:units,id',
        'lesson_id' => 'nullable|integer|exists:lessons,id',
        'ads_id' => 'nullable|integer|exists:a_d_s,id',
    ]);

    $count = collect([
        array_key_exists('subject_id', $validatedData) ? $validatedData['subject_id'] : null,
        array_key_exists('unit_id', $validatedData) ? $validatedData['unit_id'] : null,
        array_key_exists('lesson_id', $validatedData) ? $validatedData['lesson_id'] : null,
        array_key_exists('ads_id', $validatedData) ? $validatedData['ads_id'] : null,
    ])->filter()->count();

    if ($count > 1) {
        return response()->json(['error' => 'Only one of subject_id, unit_id, lesson_id, or ads_id must be selected'], 422);
    }

    if ($sender_role_id == '1' || $sender_role_id == '2' || $sender_role_id == '3') {

        // حفظ الفيديو في مجلد عام
        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');
            $videoName = time() . '.' . $videoFile->getClientOriginalExtension();
            $videoFile->move(public_path('videos'), $videoName); // حفظ الفيديو في مجلد عام
            $videoUrl = url('videos/' . $videoName); // رابط الفيديو الكامل

            // إنشاء سجل الفيديو في قاعدة البيانات
            $video = Video::create([
                'name' => $validatedData['name'],
                'video' => $videoUrl, // حفظ رابط الفيديو
                'subject_id' => $validatedData['subject_id'] ?? null,
                'unit_id' => $validatedData['unit_id'] ?? null,
                'lesson_id' => $validatedData['lesson_id'] ?? null,
                'ads_id' => $validatedData['ads_id'] ?? null,
            ]);

            return response()->json([
                'video' => $video,
                'video_url' => $videoUrl
            ], 201);
        } else {
            return response()->json(['error' => 'Video file is required'], 422);
        }
    }

    return response()->json(['error' => 'Unauthorized'], 403);
}


public function update(Request $request)
{
    $sender = Auth::user();
    $sender_role_id = $sender->role_id;

    $validatedData = $request->validate([
        'id' => 'required|integer|exists:videos,id',
        'name' => 'required|string|max:255',
        'video' => 'nullable|file|mimes:mp4,mov,avi,flv|max:204800',
    ]);

    $video = Video::find($validatedData['id']);

    if (!$video) {
        return response()->json(['error' => 'Video not found'], 404);
    }

    if ($sender_role_id != '4' ) {

         if ($request->hasFile('video')) {
            $oldVideoPath = public_path(parse_url($video->video, PHP_URL_PATH)); // مسار الفيديو القديم
            if (file_exists($oldVideoPath)) {
                unlink($oldVideoPath);
            }
            $videoFile = $request->file('video');
            $videoName = time() . '.' . $videoFile->getClientOriginalExtension();
            $videoFile->move(public_path('videos'), $videoName); // حفظ الفيديو في مجلد عام
            $videoUrl = url('videos/' . $videoName); // رابط الفيديو الكامل
            $video->video = $videoUrl;
        }

        $video->name = $validatedData['name'];
        $video->save();

        return response()->json($video, 200);
    }

    return response()->json(['error' => 'Unauthorized'], 403);
}


    public function destroy(Request $request)
    {
        $sender = Auth::user();
        $sender_role_id= $sender->role_id;
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:videos,id'
        ]);
        if ( ($sender_role_id != '4' ) ) {

        $video = Video::find($validatedData['id']);

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        Storage::delete($video->video);

        $video->delete();

        return response()->json(['message' => 'Video deleted successfully'], 200);
    }}
}
