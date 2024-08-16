<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProgressController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'video_ids' => 'required|array',
            'video_ids.*' => 'exists:videos,id',
        ]);

        $progress = Progress::where('user_id', $user->id)
                            ->where('subject_id', $request->subject_id)
                            ->first();

        $completedVideos = $progress ? json_decode($progress->completed_videos, true) : [];

        if (!is_array($completedVideos)) {
            $completedVideos = [];
        }

        $updatedVideos = array_unique(array_merge($completedVideos, $request->video_ids));

        $updatedVideosJson = json_encode($updatedVideos);

        $progress = Progress::updateOrCreate(
            [
                'user_id' => $user->id,
                'subject_id' => $request->subject_id,
            ],
            [
                'completed_videos' => $updatedVideosJson,
            ]
        );

        $totalVideos = Video::where('subject_id', $request->subject_id)->count();

        $progressPercentage = ($totalVideos > 0) ? (count($updatedVideos) / $totalVideos) * 100 : 0;

        return response()->json([
            'message' => 'Progress saved successfully',
            'progress_percentage' => $progressPercentage,
            'status' => 200,
        ]);
    }

    public function getProgress($user_id, $subject_id)
    {
        $totalVideos = Video::where('subject_id', $subject_id)->count();
        $completedVideos = Progress::where('user_id', $user_id)
                                    ->where('subject_id', $subject_id)
                                    ->sum('completed_videos');

        $progressPercentage = ($totalVideos > 0) ? ($completedVideos / $totalVideos) * 100 : 0;

        return response()->json([
            'message' => 'Progress retrieved successfully',
            'progress_percentage' => $progressPercentage,
            'completed_videos' => $completedVideos,
            'total_videos' => $totalVideos,
            'status' => 200,
        ]);
    }

    // Get all progress for a user across all subjects
    public function indexPerUser()
    {
        $user = Auth::user();
        $progressData = [];

        $progressRecords = Progress::where('user_id', $user->id)->get();

        foreach ($progressRecords as $progress) {

            $totalVideos = Video::where('subject_id', $progress->subject_id)->count();

            $completedVideos = json_decode($progress->completed_videos, true);

            if (!is_array($completedVideos)) {
                $completedVideos = [];
            }

            $progressPercentage = ($totalVideos > 0) ? (count($completedVideos) / $totalVideos) * 100 : 0;

            $progressData[] = [
                'subject_id' => $progress->subject_id,
                'completed_videos' => $completedVideos,
                'total_videos' => $totalVideos,
                'progress_percentage' => $progressPercentage,
            ];
        }

        return response()->json([
            'message' => 'All progress retrieved successfully',
            'data' => $progressData,
        ], 200);
    }
}

