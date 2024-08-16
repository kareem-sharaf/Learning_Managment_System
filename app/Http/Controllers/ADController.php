<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\AD;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Video;

class ADController extends Controller
{
    //  index all ads
    public function index()
    {
        $ads = AD::all();

            return response()->json(
                ['message' => $ads],
                200
            );

    
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'category' => 'required|exists:categories,category',
            'subject_id' => 'exists:subjects',
            'video' => 'nullable|mimes:mp4,mov,avi,flv|max:204800',
            'video_name' => 'nullable|string|max:255'
        ]);

        $category = Category::where('category', $request->category)->first();

        $adData = [
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $category->id,
            'subject_id' => $request->subject_id,
        ];

        $imagePath = $request->file('image')->store('ad_images', 'public');
        $imageUrl = Storage::url($imagePath);
        $adData['image_url'] = $imageUrl;

        $ad = AD::create($adData);

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
            $videoUrl = Storage::url($videoPath);

            $video = Video::create([
                'name' => $request->video_name,
                'video' => $videoUrl,
                'ad_id' => $ad->id
            ]);

            $ad->video_id = $video->id;
            $ad->save();
        }

        return response()->json(
            [
                'message' => 'AD added successfully',
                'ad' => $ad,
                'video' => isset($video) ? $video : null
            ],
            200
        );
    }


    //  show specific ad details
    public function show(Request $request)
    {
        $ad = AD::with('videos')->find($request->ad_id);

        if (!$ad) {
            return response()->json(
                ['error' => 'Ad not found'],
                404
            );
        }

        return response()->json(
            ['ad' => $ad],
            200
        );
    }

    //  show last 6 ads added
    public function showNewest()
    {
        $user = Auth::user();

        $favoriteCategories = $user->favorites
            ->where('favoritable_type', 'App\Models\Category')
            ->pluck('favoritable_id');

        if ($favoriteCategories->isEmpty()) {
            $newestADs = AD::where('isExpired', 0)
                ->orderBy('id', 'desc')
                ->take(6)
                ->get();
        } else {
            $newestADs = AD::where('isExpired', 0)
                ->whereIn('category_id', $favoriteCategories)
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

            if ($newestADs->isEmpty()) {
                $newestADs = AD::where('isExpired', 0)
                    ->orderBy('id', 'desc')
                    ->take(6)
                    ->get();
            }
        }
        $favoriteSubjects = $user->favorites
            ->where('favoritable_type', 'App\Models\Subject')
            ->pluck('favoritable_id');

        if ($favoriteCategories->isNotEmpty()) {
            $newSubjects = Subject::whereIn('category_id', $favoriteCategories)
                ->whereNotIn('id', $favoriteSubjects)
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

                if ($newSubjects->isEmpty()) {
                $newSubjects = Subject::whereNotIn('id', $favoriteSubjects)
                    ->inRandomOrder()
                    ->take(6)
                    ->get();
            }
        } else {
            $newSubjects = Subject::whereNotIn('id', $favoriteSubjects)
                ->inRandomOrder()
                ->take(6)
                ->get();
        }
        return response()->json([
            'newestAds' => $newestADs,
            'newSubjects' => $newSubjects
        ], 200);
    }

    // Update an ad
    public function update(Request $request)
    {
        $request->validate([
            'ad_id' => 'required|exists:a_d_s,id|numeric',
            'title' => 'string|max:255',
            'description' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'video' => 'mimes:mp4,mov,avi,flv|max:204800',
            'video_name' => 'string|max:255'
        ]);

        $ad = AD::findOrFail($request->ad_id);

        $adUpdated = false;
        $videoUpdated = false;
        $video = $ad->video;

        if ($request->filled('title') && $ad->title !== $request->title) {
            $ad->title = $request->title;
            $adUpdated = true;
        }

        if ($request->filled('description') && $ad->description !== $request->description) {
            $ad->description = $request->description;
            $adUpdated = true;
        }

        if ($request->hasFile('image')) {
            if ($ad->image_url) {
                $oldImagePath = str_replace('/storage', 'public', $ad->image_url);
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
            }

            $imagePath = $request->file('image')->store('ad_images', 'public');
            $ad->image_url = Storage::url($imagePath);
            $adUpdated = true;
        }

        if ($request->hasFile('video')) {
            if ($video) {
                $oldVideoPath = str_replace('/storage', 'public', $video->video);
                if (Storage::exists($oldVideoPath)) {
                    Storage::delete($oldVideoPath);
                }
            } else {
                $video = new Video();
                $video->ad_id = $ad->id;
            }

            $videoPath = $request->file('video')->store('videos', 'public');
            $video->video = Storage::url($videoPath);
            $videoUpdated = true;
        }

        if ($request->filled('video_name')) {
            if (!$video) {
                $video = new Video();
                $video->ad_id = $ad->id;
            }
            $video->name = $request->video_name;
            $videoUpdated = true;
        }

        if ($videoUpdated) {
            $video->save();
        }

        if ($adUpdated || $videoUpdated) {
            if ($ad->isDirty()) {
                $ad->save();
            }

            return response()->json(
                [
                    'message' => 'Ad updated successfully',
                    'ad' => $ad->load('video')
                ],
                200
            );
        } else {
            return response()->json(['error' => 'Nothing to update'], 400);
        }
    }


    //  set the ad to be expired
    public function setExpired(Request $request)
    {
        $user = Auth::user();

        $ad = AD::where('id', $request->ad_id)
            ->first();
        if ($ad && $ad->isExpired == 0) {
            $ad->isExpired = 1;
            $ad->save();
            return response()->json(
                ['message' => 'ad is now set to expired!'],
                200
            );
        }
        return response()->json(
            ['error' => 'ad is already expired!'],
            404
        );
    }

    // delete an ad
    public function destroy(Request $request)
    {
        $ad = AD::where('id', $request->ad_id)->first();

        if ($ad) {
            $videos = $ad->videos;

            if ($videos) {
                if ($videos->video) {
                    $oldVideoPath = str_replace('/storage', 'public', $videos->video);
                    if (Storage::exists($oldVideoPath)) {
                        Storage::delete($oldVideoPath);
                    }
                }
                $videos->delete();
            }

            if ($ad->image_url) {
                $oldImagePath = str_replace('/storage', 'public', $ad->image_url);
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
            }

            $ad->delete();

            return response()->json(
                ['message' => 'Ad and associated video deleted successfully'],
                200
            );
        }

        return response()->json(
            ['error' => 'Ad not found'],
            404
        );
    }
}
