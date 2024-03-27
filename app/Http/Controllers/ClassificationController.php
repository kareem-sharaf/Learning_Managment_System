<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classification;
use Illuminate\Support\Facades\Artisan;

class ClassificationController extends Controller
{
    //  index all classifications
    public function index()
    {
        $classifications = Classification::all();
        return response()->json(
            [$classifications],
            200
        );
    }

    public function show(Request $request)
    {
        $class = Classification::where('class', $request->class)->first();

        if ($class) {
            return response()->json(['class' => $class], 200);
        }

        return response()->json(['error' => 'Class not found!'], 404);
    }
    //  show the subjects of a specific class
    public function store(Request $request)
    {
        $request->validate([
            'class' => 'required|string|unique:classifications',
        ]);

        $classification = Classification::create([
            'class' => $request->class,
        ]);

        return response()->json(
            [
                'message' => 'Classification created successfully',
                'classification' => $classification
            ],
            201
        );
    }

    public function destroy(Request $request)
    {
        $classification = Classification::find($request->class_id);

        if (!$classification) {
            return response()->json(
                ['error' => 'Classification not found!'],
                404
            );
        }

        $classification->delete();

        return response()->json(
            ['message' => 'Classification deleted successfully'],
            200
        );
    }

    // seed classifications
    public function seedClassification()
    {
        Artisan::call('db:seed', ['--class' => 'ClassificationSeeder']);
    }
}
