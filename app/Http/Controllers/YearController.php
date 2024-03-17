<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\Stage;

class YearController extends Controller
{
    //  seed years
    public function seedYears()
    {
        Artisan::call('db:seed', ['--class' => 'YearSeeder']);
    }

    //  index all years
    public function index()
    {
        $years = Year::all();
        return response()->json(['years' => $years]);
    }

    //  search for a year
    public function search(Request $request)
    {
        $request->validate([
            'year' => 'required|string'
        ]);

        $years = Year::where('year', 'like', '%' . $request->year . '%')
            ->get();

        if ($years->isNotEmpty()) {
            return response()->json(
                ['message' => $years],
                200
            );
        }
        return response()->json(
            ['message' => 'year not found!'],
            404
        );
    }

    //  add a new year
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'year' => 'required|string',
            'stage' => 'required|string'
        ]);

        $year = Year::where('year', $request->year)
            ->first();

        if (!$year) {
            $stage = Stage::where('stage', $request->stage)
                ->first();
            $newYear = Year::create([
                'year' => $request->year,
                'stage_id' => $stage->id
            ]);

            return response()->json(
                ['message' => 'Year added successfully', 'Year' => $newYear],
                201
            );
        } else {
            return response()->json(
                ['message' => 'Year already exists'],
                200
            );
        }
    }

    //  update an existing year
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'year' => 'required|string|exists:years,year',
            'stage' => 'required|string',
            'newYear' => 'required|string',
            'newStage' => 'required|string'
        ]);

        $year = Year::where('year', $request->year)
            ->first();

        $newYear = Year::where('year', $request->newYear)
            ->first();

        if ($newYear) {
            return response()->json(
                ['error' => 'Year alredy exists!'],
                404
            );
        }
        if ($year->year === $newYear) {
            return response()->json(
                ['error' => 'nothing to update!'],
                404
            );
        }

        $year->Year = $request->newYear;
        $year->save();

        return response()->json(
            ['message' => 'Year updated successfully'],
            200
        );
    }

    // delete a year
    public function destroy(Request $request)
    {
        $user = Auth::user();

        $stage = Stage::where('stage', $request->stage)
            ->first();

        $year = Year::where('year', $request->year)
            ->where('stage_id', $stage->id)
            ->first();

        if ($year) {
            $year->delete();
            return response()->json(
                ['message' => 'Successfully deleted Year!'],
                200
            );
        }
        return response()->json(
            ['message' => 'Year not found!'],
            404
        );
    }
}
