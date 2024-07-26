<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;
<<<<<<< HEAD
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
=======

use Illuminate\Support\Facades\Auth;
>>>>>>> 4daf2304093ea2d807c7f190a68be2a2151cc98b

class VideoController extends Controller
{
    public function store(Request $request)
<<<<<<< HEAD
    {
        $sender = Auth::user();
        $sender_role_id= $sender->role_id;

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'video' => 'required|file|mimes:mp4',
=======
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'video' => 'required|file|mimes:mp4',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'lesson_id' => 'nullable|integer|exists:lessons,id',
            'ads_id' => 'nullable|integer|exists:a_d_s,id'
        ]);

        $videoFile = $request->file('video');
        $videoPath = $videoFile->store('videos', 'public');

        $sender = Auth::user();
        $sender_role_id= $sender->role_id;

        if (($sender_role_id == '2' || $sender_role_id == '3') ) {

        $video = Video::create([
            'name' => $validatedData['name'],
            'video' => $videoPath,
            'subject_id' => $validatedData['subject_id'] ?? null,
            'unit_id' => $validatedData['unit_id'] ?? null,
            'lesson_id' => $validatedData['lesson_id'] ?? null,
            'ads_id' => $validatedData['ads_id'] ?? null,
        ]);

        return response()->json($video, 201);
    }}

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:videos,id'
        ]);

        $video = Video::find($validatedData['id']);

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'video' => 'nullable|file|mimes:mp4',
>>>>>>> 4daf2304093ea2d807c7f190a68be2a2151cc98b
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'lesson_id' => 'nullable|integer|exists:lessons,id',
            'ads_id' => 'nullable|integer|exists:a_d_s,id'
        ]);

<<<<<<< HEAD
        $count = collect([$validatedData['subject_id'], $validatedData['unit_id'], $validatedData['lesson_id'], $validatedData['ads_id']])
            ->filter()
            ->count();

        if ($count > 1) {
            return response()->json(['error' => 'Only one of subject_id, unit_id, lesson_id, or ads_id must be selected'], 422);
        }
        if (($sender_role_id == '2' || $sender_role_id == '3') ) {

        $videoFile = $request->file('video');
        $videoPath = $videoFile->storeAs('videos', $videoFile->getClientOriginalName(), 'public');

        $video = Video::create([
            'name' => $validatedData['name'],
            'video' => $videoPath,
            'subject_id' => $validatedData['subject_id'] ?? null,
            'unit_id' => $validatedData['unit_id'] ?? null,
            'lesson_id' => $validatedData['lesson_id'] ?? null,
            'ads_id' => $validatedData['ads_id'] ?? null,
        ]);

        $videoUrl = Storage::url($videoPath);

        return response()->json([
            'video' => $video,
            'video_url' => $videoUrl
        ], 201);
    }}

    public function update(Request $request)
    {

        $sender = Auth::user();
        $sender_role_id= $sender->role_id;

        $validatedData = $request->validate([
            'id' => 'required|integer|exists:videos,id'
        ]);

        $video = Video::find($validatedData['id']);

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }
        if (($sender_role_id == '2' || $sender_role_id == '3') ) {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'video' => 'nullable|file|mimes:mp4',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'lesson_id' => 'nullable|integer|exists:lessons,id',
            'ads_id' => 'nullable|integer|exists:a_d_s,id'
        ]);

        $count = collect([$validatedData['subject_id'], $validatedData['unit_id'], $validatedData['lesson_id'], $validatedData['ads_id']])
            ->filter()
            ->count();

        if ($count > 1) {
            return response()->json(['error' => 'Only one of subject_id, unit_id, lesson_id, or ads_id must be selected'], 422);
        }

        if ($request->hasFile('video')) {
            Storage::delete($video->video);
            $videoFile = $request->file('video');
            $videoPath = $videoFile->storeAs('videos', $videoFile->getClientOriginalName(), 'public');
=======
        $sender = Auth::user();
        $sender_role_id= $sender->role_id;

        if (($sender_role_id == '2' || $sender_role_id == '3') ) {
        if ($request->hasFile('video')) {
            Storage::delete($video->video);
            $videoFile = $request->file('video');
        $videoPath = $videoFile->store('videos', 'public');

>>>>>>> 4daf2304093ea2d807c7f190a68be2a2151cc98b
            $video->video = $videoPath;
        }

        $video->name = $validatedData['name'];
        $video->subject_id = $validatedData['subject_id'] ?? $video->subject_id;
        $video->unit_id = $validatedData['unit_id'] ?? $video->unit_id;
        $video->lesson_id = $validatedData['lesson_id'] ?? $video->lesson_id;
        $video->ads_id = $validatedData['ads_id'] ?? $video->ads_id;

        $video->save();

        return response()->json($video, 200);
    }}

    public function destroy(Request $request)
    {
<<<<<<< HEAD
        $sender = Auth::user();
        $sender_role_id= $sender->role_id;
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:videos,id'
        ]);
        if (($sender_role_id == '2' || $sender_role_id == '3') ) {

        $video = Video::find($validatedData['id']);

=======

        $validatedData = $request->validate([
            'id' => 'required|integer|exists:videos,id'
        ]);

        $video = Video::find($validatedData['id']);

        $sender = Auth::user();
        $sender_role_id= $sender->role_id;

        if (($sender_role_id == '2' || $sender_role_id == '3') ) {
>>>>>>> 4daf2304093ea2d807c7f190a68be2a2151cc98b
        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        Storage::delete($video->video);

        $video->delete();

        return response()->json(['message' => 'Video deleted successfully'], 200);
<<<<<<< HEAD
    }}
=======
    }
}
>>>>>>> 4daf2304093ea2d807c7f190a68be2a2151cc98b
}
