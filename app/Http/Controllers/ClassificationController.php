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

    //  show the subjects of a specific class
    public function show(Request $request)
    {
    }

    public function store(Request $request)
    {
    }


    // seed classifications
    public function seedClassification()
    {
        Artisan::call('db:seed', ['--class' => 'ClassificationSeeder']);
    }
}
