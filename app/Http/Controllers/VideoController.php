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
        'videos' => 'required|array',
        'videos.*' => 'required|file|mimes:mp4|max:10240',
        'subject_id' => '|integer|exists:subjects,id',
        'unit_id' => '|integer|exists:units,id',
        'lesson_id' => '|integer|exists:lessons,id',
        'ads_id' => '|integer|exists:a_d_s,id'
    ]);

    $videos = [];
    foreach ($request->file('videos') as $videoFile) {
        $videoPath = $videoFile->store('videos', 'public');
        $videos[] = Video::create([
            'name' => $validatedData['name'],
            'video' => $videoPath,
            'subject_id' => $validatedData['subject_id'],
            'unit_id' => $validatedData['unit_id'],
            'lesson_id' => $validatedData['lesson_id'],
            'ads_id' => $validatedData['ads_id'],
        ]);
    }

    return response()->json($videos, 201);
}

public function update(Request $request)
    {
        $id = $request->id;
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'video' => 'nullable|file|mimes:mp4|max:10240', // 10MB max file size
            'subject_id' => '|integer|exists:subjects,id',
            'unit_id' => '|integer|exists:units,id',
            'lesson_id' => '|integer|exists:lessons,id',
            'ads_id'=>'|integer|exists:a_d_s,id'
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

    public function destroy(Request $request)
    {
        $id = $request->id;
    
        $video = Video::find($id);
    
        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }
    
        Storage::delete($video->video);
    
        $video->delete();
    
        return response()->json(['message' => 'Video deleted successfully'], 200);
    }
}

