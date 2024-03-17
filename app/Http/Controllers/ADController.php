<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
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
            'image_data' => 'required',
        ]);

        $year = Year::where('id', $request->year_id)
            ->first();
        $stage = null;
        if ($year) {
            $stage = Stage::where('id', $year->stage_id)
                ->first();
        }

        $adData = [
            'title' => $request->title,
            'description' => $request->description,
            'year_id' => $year ? $year->id : null,
            'stage_id' => $stage ? $stage->id : null,
        ];

        $image = $request->file('image');
        if ($image) {
            $imageData = base64_encode(file_get_contents($image->path()));
            $adData['image_data'] = $imageData;
        }

        $ad = AD::create($adData);

        return response()->json(
            ['message' => 'AD added successfully'],
            200
        );
    }

    //  show last 6 ads added
    public function show()
    {
        $newestAD = AD::orderBy('id', 'desc')
            ->first();
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

    // update an ad
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ad_id' => 'required|exists:a_d_s,id|numeric',
            'title' => 'string|max:255',
            'description' => 'string',
            'image_data' => 'image',
            'year' => 'string'
        ]);

        $year = Year::where('year', $request->year)
            ->first();

        $ad = AD::where('id', $request->ad_id)
            ->first();

        if (
            $request->filled('title') &&
            $ad->title !== $request->title
        ) {
            $ad->title = $request->title;
        }

        if (
            $request->filled('description') &&
            $ad->description !== $request->description
        ) {
            $ad->description = $request->description;
        }

        if (
            $request->filled('year') &&
            $ad->year_id !== $year->id
        ) {
            $ad->year_id = $year->id;
            $ad->stage_id = $year->stage_id;
        }

        if ($request->hasFile('image_data')) {
            $image = $request->file('image_data');
            $imageData = base64_encode(file_get_contents($image->path()));
            $ad->image_data = $imageData;
        }

        if (!$ad->isDirty()) {
            return response()->json(
                ['error' => 'Nothing to update'],
                400
            );
        }

        $ad->save();

        return response()->json(
            ['message' => 'Ad updated successfully'],
            200
        );
    }

    //  set the ad to be expired
    public function setExpired(Request $request)
    {
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
        $ad = AD::where('id', $request->ad_id)
            ->first();
        if ($ad){
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
