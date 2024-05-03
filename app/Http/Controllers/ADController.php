<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\AD;
use App\Models\Year;
use App\Models\Stage;

class ADController extends Controller
{
    //  index all ads
    public function index()
    {
        $ads = AD::all();
        if ($ads->isNotEmpty()) {
            return response()->json(
                ['message' => $ads],
                200
            );
        }
        return response()->json(
            ['message' => 'no ads has been found'],
            404
        );
    }

    //  add a new ad
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'year_id' => 'exists:years'
        ]);

        $year = Year::find($request->year_id);
        $stage = $year ? Stage::find($year->stage_id) : null;

        $adData = [
            'title' => $request->title,
            'description' => $request->description,
            'year_id' => $year ? $year->id : null,
            'stage_id' => $stage ? $stage->id : null,
        ];

        $imagePath = $request->file('image')->store('ad_images', 'public');

        $imageUrl = Storage::url($imagePath);

        $adData['image_url'] = $imageUrl;

        AD::create($adData);

        return response()->json(['message' => 'AD added successfully'], 200);
    }

    //  show specific ad details
    public function show(Request $request)
    {
        $ad = AD::find($request->ad_id);

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
        $newestAD = AD::orderBy('id', 'desc')
            ->first();
        if ($newestAD) {
            $maxValue = $newestAD->id;
            $newestADs = [];
            for ($i = 0; $i < 6; $i++) {
                $ad = AD::where('id', $maxValue)
                    ->first();

                if ($ad && $ad->isExpired == 0) {
                    $newestADs[$i] = $ad;
                    $maxValue--;
                } else {
                    $maxValue--;
                    $i--;
                }
                if ($maxValue == 0)
                    break;
            }
            return response()->json(
                ['message' => $newestADs],
                200
            );
        }
        return response()->json(
            ['error' => 'no new ads found'],
            404
        );
    }

    // update an ad
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ad_id' => 'required|exists:a_d_s,id|numeric',
            'title' => 'string|max:255',
            'description' => 'string',
            'image' => 'image', // Change validation rule for image
            'year' => 'string'
        ]);

        $ad = AD::findOrFail($request->ad_id);

        if ($request->filled('title') && $ad->title !== $request->title) {
            $ad->title = $request->title;
        }

        if ($request->filled('description') && $ad->description !== $request->description) {
            $ad->description = $request->description;
        }

        if ($request->filled('year')) {
            $year = Year::where('year', $request->year)->first();
            if ($year && $ad->year_id !== $year->id) {
                $ad->year_id = $year->id;
                $ad->stage_id = $year->stage_id;
            }
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($ad->image_url) {
                $oldImagePath = str_replace('/storage', 'public', $ad->image_url);
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
            }

            // Store new image
            $imagePath = $request->file('image')->store('ad_images', 'public');
            $ad->image_url = Storage::url($imagePath);
        }

        if (!$ad->isDirty()) {
            return response()->json(['error' => 'Nothing to update'], 400);
        }

        $ad->save();

        return response()->json(['message' => 'Ad updated successfully'], 200);
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
        $user = Auth::user();

        $ad = AD::where('id', $request->ad_id)
            ->first();
        if ($ad) {
            $ad->delete();
            return response()->json(
                ['message' => 'ad deleted successfully'],
                200
            );
        }
        return response()->json(
            ['error' => 'ad not found'],
            404
        );
    }
}
