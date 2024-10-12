<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class VideoService
{
    // Method to replace the video
    public function replaceVideo($newVideo, $oldVideoPath, $videoName)
    {
        // Delete old video if it exists
        if ($oldVideoPath && Storage::disk('public')->exists(basename($oldVideoPath))) {
            Storage::disk('public')->delete(basename($oldVideoPath));
        }

        // Upload new video
        return $this->uploadVideo($newVideo, $videoName);
    }

    // Existing upload method
    public function uploadVideo($video, $videoName)
    {
        $videoPath = $video->store('videos', 'public');
        return [
            'path' => Storage::url($videoPath),
            'name' => $videoName,
        ];
    }
}
