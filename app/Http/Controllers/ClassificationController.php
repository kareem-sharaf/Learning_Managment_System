<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classification;
use App\Models\Subject;
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

    //  show the subjects of a specific class
    public function show(Request $request)
    {
        $class = Classification::where('class', $request->class)->first();
        $subjects = Subject::where('class_id', $class->id)->get();

        if ($class) {
            $subjects = $class->subjects;

            return response()->json(
                ['message' => 'Subjects of this class:', 'subjects' => $subjects],
                200
            );
        }

        return response()->json(
            ['message' => 'Class not found!'],
            404
        );
    }

    //  store new class
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

    //  delete class
    public function destroy(Request $request)
    {
        $classification = Classification::where('class', $request->class)->first();

        if (!$classification) {
            return response()->json(
                ['message' => 'Classification not found!'],
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
