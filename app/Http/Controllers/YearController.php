<?php

namespace App\Http\Controllers;

use App\Models\Year;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

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
        $Years = Year::all();
        return response()->json([$Years]);
    }

    //  search for a year
    public function search(Request $request)
    {
        $request->validate([
            'year' => 'required|string'
        ]);

        $years = Year::where('year', 'like', '%' . $request->Year . '%')
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
    public function add(Request $request)
    {
        // $user = Auth::user();

        // if ($user->role != (1 || 2)) {
        //     return response()->json(
        //         ['error' => 'Unauthorized'],
        //         403
        //     );
        // }

        $request->validate([
            'year' => 'required|string'
        ]);

        $YearName = $request->Year;

        $existingYear = Year::where('year', $YearName)
            ->first();

        if (!$existingYear) {
            $newYear = Year::create([
                'Year' => $YearName
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
        // $user = Auth::user();

        // if ($user->role != (1 || 2)) {
        //     return response()->json(
        //         ['error' => 'Unauthorized'],
        //         403
        //     );
        // }

        $request->validate([
            'year' => 'required|exists:years,year',
            'newYear' => 'required|string'
        ]);

        $year = Year::where('year', $request->year)
            ->first();
        $newYear = $request->newYear;

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
        // $user = Auth::user();

        // if ($user->role != (1 || 2)) {
        //     return response()->json(
        //         ['error' => 'Unauthorized'],
        //         403
        //     );
        // }

        $year = Year::where('Year', $request->Year)
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
