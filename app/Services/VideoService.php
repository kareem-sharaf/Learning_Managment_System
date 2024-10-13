<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class VideoService
{


    public function uploadVideo($video)
    {
        $videoName = time() . '.' . $video->getClientOriginalExtension();
        $video->move(public_path('videos'), $videoName);

        return [
            'path' => url('videos/' . $videoName),
            'name' => $videoName
        ];
    }

    public function replaceVideo($newVideo, $oldVideoPath)
    {
        if ($oldVideoPath && file_exists(public_path('videos/' . basename($oldVideoPath)))) {
            unlink(public_path('videos/' . basename($oldVideoPath)));
        }

        return $this->uploadVideo($newVideo);
    }
}
