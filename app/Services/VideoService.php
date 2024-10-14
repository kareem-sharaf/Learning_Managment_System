<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Video;

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

    public function replaceVideo($newVideo, $type, $videoName)
    {

        $videos = Video::where('type_id', $type->id)->get();

        foreach ($videos as $video) {
            $oldVideoPath = public_path('videos/' . basename($video->video));
            if (file_exists($oldVideoPath)) {
                unlink($oldVideoPath);
            }

            $video->delete();
        }

        return $this->saveVideo($newVideo, $type, $videoName);
    }

    public function saveVideo($video, $type, $videoName)
    {
        $videoDetails = $this->uploadVideo($video);

        $newVideo = new Video();
        $newVideo->video = $videoDetails['path'];
        $newVideo->name = $videoName;
        $newVideo->type_id = $type->id;
        $newVideo->type_type = get_class($type);
        $newVideo->exist = true;
        $newVideo->save();

        return $newVideo;
    }


    public function deleteVideos($type_id)
    {
        Video::where('type_id',$type_id)
            ->update(['exist' => false]);
    }
}
