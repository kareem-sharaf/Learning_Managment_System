<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Lesson;

class VideoController extends Controller
{
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'video' => 'required|file|mimes:mp4|max:10240', 
        'subject_id' => 'required|integer|exists:subjects,id',
        'unit_id' => 'required|integer|exists:units,id',
        'lesson_id' => 'required|integer|exists:lessons,id',
    ]);

    $videoFile = $request->file('video');
    $videoPath = $videoFile->store('videos', 'public');

    $video = Video::create([
        'name' => $validatedData['name'],
        'video' => $videoPath,
        'subject_id' => $validatedData['subject_id'],
        'unit_id' => $validatedData['unit_id'],
        'lesson_id' => $validatedData['lesson_id'],
    ]); 

    return response()->json($video, 201);
}
  public function update(Request $request, $id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'equired|string|max:255',
            'video' => 'nullable|file|mimes:mp4|max:10240', // 10MB max file size
            'ubject_id' => 'equired|integer|exists:subjects,id',
            'unit_id' => 'equired|integer|exists:units,id',
            'lesson_id' => 'equired|integer|exists:lessons,id',
        ]);

        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');
            $videoPath = $videoFile->store('videos', 'public');
            $video->video = $videoPath;
        }

        $video->name = $validatedData['name'];
        $video->subject_id = $validatedData['subject_id'];
        $video->unit_id = $validatedData['unit_id'];
        $video->lesson_id = $validatedData['lesson_id'];

        $video->save();

        return response()->json($video, 200);
    }

    public function destroy($id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        Storage::delete($video->video);

        $video->delete();

        return response()->json(['message' => 'Video deleted successfully'], 200);
    }
}

