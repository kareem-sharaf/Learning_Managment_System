<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use alchemyguy\YoutubeLaravelApi\VideoService;

class YoutubeVideoController extends Controller
{
    public $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function uploadVideo(Request $request)
    {
        try{
        $googleToken = env('YOUTUBE_API_KEY'); // Use the YouTube API key from your .env file
        $videoPath = $request->file('video'); // Get the video file from the request
        $data = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'tags' => $request->input('tags'),
            'categoryId' => $request->input('category_id'),
            'privacyStatus' => $request->input('privacy_status'),
        ];

        $response = $this->videoService->uploadVideo($googleToken, $videoPath, $data);
      
        // Store video data in the database
        if ($response->getStatusCode() == 200) {
            $youtubeVideo = new YoutubeVideo();
            $youtubeVideo->video_id = $response->getId()->getVideoId();
            $youtubeVideo->title = $response->getSnippet()->getTitle();
            $youtubeVideo->description = $response->getSnippet()->getDescription();
            $youtubeVideo->thumbnail_url = $response->getSnippet()->getThumbnails()->getDefault()->getUrl();
            $youtubeVideo->video_url = 'https://www.youtube.com/watch?v='. $response->getId()->getVideoId();
            $youtubeVideo->views = 0;
            $youtubeVideo->likes = 0;
            $youtubeVideo->dislikes = 0;
            $youtubeVideo->category_id = $response->getSnippet()->getCategoryId();
            $youtubeVideo->privacy_status = $response->getSnippet()->getPrivacyStatus();
            $youtubeVideo->save();

        
    }
    return response()->json(['message' => 'Video uploaded successfully'], 201);} 
 catch (\Exception $e) {
    return response()->json(['error' => 'There was an error uploading the video. Please try again later.'], 500);
}

}
}