<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\YouTube;
use Google\Http\MediaFileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\YouTube1; // Changed from YouTube to YouTube1
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Lesson;
use App\Models\Ad;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    public function authenticate(Request $request)
    {
        $redirectUrl = route('youtube.callback'); // Route to handle OAuth callback

        $client = new Client();
        $client->setAuthConfig(base_path('youtube.json'));
        $client->setRedirectUri($redirectUrl);
        $client->addScope('https://www.googleapis.com/auth/youtube.upload');
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);

        if ($request->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->input('code'));
            if (isset($token['access_token'])) {
                $client->setAccessToken($token);
                Session::put('google_oauth_token', $token);
                Session::put('google_oauth_refresh_token', $token['refresh_token'] ?? null);

                return response()->json([
                    'message' => 'Authenticated successfully.',
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'] ?? null
                ], 200);
            } else {
                return response()->json(['error' => 'Failed to obtain access token.'], 400);
            }
        }
        // Generate the authorization URL
        if (!Session::has('google_oauth_token')) {
            $authUrl = $client->createAuthUrl();
            return response()->json(['auth_url' => $authUrl], 200);
        }

        return response()->json(['message' => 'Already authenticated.'], 200);
    }

    public function callback(Request $request)
    {
        return $this->authenticate($request);
    }


    public function upload(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'video' => 'required|mimes:mp4', // Adjust max size if needed
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'lesson_id' => 'nullable|integer|exists:lessons,id',
            'ads_id' => 'nullable|integer|exists:ads,id',
            'access' => 'required|string',
            'refresh' => 'required|string'
        ]);
        $sender = Auth::user();
        $sender_role_id= $sender->role_id;
        // Ensure only one of subject_id, unit_id, lesson_id, or ads_id is provided
        $count = collect([
            $validatedData['subject_id'],
            $validatedData['unit_id'],
            $validatedData['lesson_id'],
            $validatedData['ads_id']
        ])->filter()->count();

        if ($count > 1) {
            return response()->json(['error' => 'Only one of subject_id, unit_id, lesson_id, or ads_id must be selected'], 422);
        }
        if (($sender_role_id == '2' || $sender_role_id == '3'||$sender_role_id == '1') ) {
        ini_set('max_execution_time', '300'); // 5 minutes
        ini_set('memory_limit', '512M'); // Increase as necessary

        $accessToken = $request->input('access');
        $refreshToken = $request->input('refresh');
        $videoPath = $request->file('video')->getPathname();
        $videoTitle = $request->input('title');
        $videoDescription = $request->input('description');

        $client = new Client();
        $client->setAuthConfig(base_path('youtube.json'));
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        $client->addScope('https://www.googleapis.com/auth/youtube.upload');

        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            if ($refreshToken) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                $client->setAccessToken($newToken);
                $accessToken = $newToken['access_token'];
            } else {
                return response()->json(['error' => 'Refresh token is missing or expired.'], 401);
            }
        }

        $service = new YouTube($client);

        $snippet = new \Google\Service\YouTube\VideoSnippet();
        $snippet->setTitle($videoTitle);
        $snippet->setDescription($videoDescription);

        $status = new \Google\Service\YouTube\VideoStatus();
        $status->setPrivacyStatus('unlisted'); // Change from 'public' to 'unlisted'

        $video = new \Google\Service\YouTube\Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        $chunkSizeBytes = 1 * 1024 * 1024; // 1MB

        $client->setDefer(true);
        $insertRequest = $service->videos->insert('snippet,status', $video);

        $media = new MediaFileUpload(
            $client,
            $insertRequest,
            'video/*',
            null,
            true,
            $chunkSizeBytes
        );

        $media->setFileSize(filesize($videoPath));

        $status = false;
        $handle = fopen($videoPath, 'rb');
        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }
        fclose($handle);

        $client->setDefer(false);

        $videoId = $status['id'];
        $videoUrl = 'https://www.youtube.com/watch?v=' . $videoId;

        try {
            // Use a transaction to ensure atomicity
            DB::beginTransaction();

            // Insert into the youtube1 table
            DB::table('youtube1')->insert([
                'title' => $videoTitle,
                'description' => $videoDescription,
                'tags' => $request->input('tags'),
                'video_id' => $videoId,
                'video_url' => $videoUrl,
                'subject_id' => $validatedData['subject_id'],
                'unit_id' => $validatedData['unit_id'],
                'lesson_id' => $validatedData['lesson_id'],
                'ads_id' => $validatedData['ads_id'],
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to save video information.'], 500);
        }

        return response()->json([
            'status' => 'success',
            'videoId' => $videoId,
            'title' => $status['snippet']['title'],
            'videoUrl' => $videoUrl
        ]);}
    }
    public function update(Request $request)
    {
        $sender = Auth::user();
        $sender_role_id= $sender->role_id;
        $validatedData = $request->validate([
            'video_id' => 'required|string',
            'tags'=>'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'lesson_id' => 'nullable|integer|exists:lessons,id',
            'ads_id' => 'nullable|integer|exists:ads,id',
        ]);

        $accessToken = $request->input('access');
        $refreshToken = $request->input('refresh');
        if (($sender_role_id == '2' || $sender_role_id == '3'||$sender_role_id == '1') ) {
        $client = new Client();
        $client->setAuthConfig(base_path('youtube.json'));
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        $client->addScope('https://www.googleapis.com/auth/youtube.upload');
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            if ($refreshToken) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                $client->setAccessToken($newToken);
                $accessToken = $newToken['access_token'];
            } else {
                return response()->json(['error' => 'Refresh token is missing or expired.'], 401);
            }
        }

        $service = new YouTube($client);
        $video = $service->videos->listVideos('snippet', ['id' => $validatedData['video_id']])->getItems()[0];
        $snippet = $video->getSnippet();
        $snippet->setTitle($validatedData['title']);
        $snippet->setDescription($validatedData['description']);

        $updateRequest = $service->videos->update('snippet', $video);
        $updateRequest->setSnippet($snippet);
        $response = $updateRequest->execute();

        // Update the record in your database
        DB::table('youtube1')->where('video_id', $validatedData['video_id'])->update([
            'title' => $validatedData['title'],
            'tags'=>$validatedData['tags'],
            'description' => $validatedData['description'],
            'subject_id' => $validatedData['subject_id'],
            'unit_id' => $validatedData['unit_id'],
            'lesson_id' => $validatedData['lesson_id'],
            'ads_id' => $validatedData['ads_id'],
        ]);

        return response()->json(['status' => 'success', 'videoId' => $validatedData['video_id']]);
    }}

    public function delete(Request $request)
    {
        $sender = Auth::user();
        $sender_role_id= $sender->role_id;
        $validatedData = $request->validate([
            'video_id' => 'required|string',
        ]);

        $accessToken = $request->input('access');
        $refreshToken = $request->input('refresh');
        if (($sender_role_id == '2' || $sender_role_id == '3'||$sender_role_id == '1') ) {
        $client = new Client();
        $client->setAuthConfig(base_path('youtube.json'));
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        $client->addScope('https://www.googleapis.com/auth/youtube.upload');
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            if ($refreshToken) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                $client->setAccessToken($newToken);
                $accessToken = $newToken['access_token'];
            } else {
                return response()->json(['error' => 'Refresh token is missing or expired.'], 401);
            }
        }

        $service = new YouTube($client);
        $deleteRequest = $service->videos->delete(['id' => $validatedData['video_id']]);
        $response = $deleteRequest->execute();

        // Delete the record in your database
        DB::table('youtube1')->where('video_id', $validatedData['video_id'])->delete();

        return response()->json(['status' => 'success', 'videoId' => $validatedData['video_id']]);
    }
    }


    // public function index()
    // {
    //     $apiKey = config('app.youtube_api_key');
    //     $client = new Client();
    //     $client->setDeveloperKey($apiKey);
    //     $service = new YouTube($client);

    //     $response = $service->videos->listVideos('snippet', ['id' => 'fG08dcJ8xFE']);

    //     dump($response);
    // }

    // public function index1(Request $request)
    // {
    //     $redirectUrl = route('youtube.callback');

    //     $client = new Client();
    //     $client->setAuthConfig(base_path('youtube.json'));
    //     $client->setRedirectUri($redirectUrl);
    //     $client->addScope('https://www.googleapis.com/auth/youtube');

    //     if (!$request->has('code') && !Session::has('google_oauth_token')) {
    //         $authUrl = $client->createAuthUrl();
    //         return response()->json(['auth_url' => $authUrl], 200);
    //     }

    //     if ($request->has('code')) {
    //         $token = $client->fetchAccessTokenWithAuthCode($request->input('code'));
    //         if (isset($token['access_token'])) {
    //             $client->setAccessToken($token);
    //             Session::put('google_oauth_token', $token);
    //             return redirect($redirectUrl);
    //         } else {
    //             return response()->json(['error' => 'Failed to obtain access token.'], 400);
    //         }
    //     }

    //     if (Session::has('google_oauth_token')) {
    //         $client->setAccessToken(Session::get('google_oauth_token'));
    //         if ($client->isAccessTokenExpired()) {
    //             Session::forget('google_oauth_token');
    //             return response()->json(['message' => 'Token expired. Please reauthenticate.'], 401);
    //         }
    //         return view('demo')->with('connected', true);
    //     }

    //     return view('demo')->with('connected', false);
    // }

    // public function edit()
    // {
    //     $videoId = 'tmrsN_-ge-k';
    //     $newTitle = 'Lionel Messi ';

    //     $client = new Client();
    //     $client->setAuthConfig(base_path('youtube.json'));

    //     if (Session::has('google_oauth_token')) {
    //         $client->setAccessToken(Session::get('google_oauth_token'));
    //     } else {
    //         return redirect('/');
    //     }

    //     $service = new YouTube($client);
    //     $response = $service->videos->listVideos('snippet', ['id' => $videoId]);
    //     $video = $response[0];
    //     $snippet = $video->snippet;
    //     $snippet->setTitle($newTitle);

    //     $video->setSnippet($snippet);

    //     $response = $service->videos->update('snippet', $video);
    //     dump($response->snippet);
    // }
}
