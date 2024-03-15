<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\AD;

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
    public function store()
    {
        
    }

    //  show last 6 ads added
    public function show()
    {
        $newestAD = AD::orderBy('id', 'desc')->first(); // gets the whole row
        $maxValue = $newestAD->id;
        $newestADs = [];
        for ($i = 0; $i < 6; $i++) {
            $ad = AD::where('id', $maxValue)->first();
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

    //  update an ad


    //  set the ad to be expired
    public function setExpired()
    {
    }

    // delete an ad
    public function destroy()
    {
    }
}
