<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;

use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    public function store(Request $request)
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
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'lesson_id' => 'nullable|integer|exists:lessons,id',
            'ads_id' => 'nullable|integer|exists:a_d_s,id'
        ]);

        $sender = Auth::user();
        $sender_role_id= $sender->role_id;

        if (($sender_role_id == '2' || $sender_role_id == '3') ) {
        if ($request->hasFile('video')) {
            Storage::delete($video->video);
            $videoFile = $request->file('video');
        $videoPath = $videoFile->store('videos', 'public');

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

        $validatedData = $request->validate([
            'id' => 'required|integer|exists:videos,id'
        ]);

        $video = Video::find($validatedData['id']);

        $sender = Auth::user();
        $sender_role_id= $sender->role_id;

        if (($sender_role_id == '2' || $sender_role_id == '3') ) {
        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        Storage::delete($video->video);

        $video->delete();

        return response()->json(['message' => 'Video deleted successfully'], 200);
    }
}
}
