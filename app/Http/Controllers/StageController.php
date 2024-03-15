<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Stage;


class StageController extends Controller
{
    //  Seed stages
    public function seedStages()
    {
        Artisan::call('db:seed', ['--class' => 'StageSeeder']);
    }

    //  Index all stages
    public function index()
    {
        $stages = Stage::all();
        return response()->json(
            ['messsage' => $stages],
            200
        );
    }

    //  search for satges
    public function search(Request $request)
    {
        $request->validate([
            'stage' => 'required|string'
        ]);

        $stages = Stage::where('stage', 'like', '%' . $request->stage . '%')
            ->get();

        if ($stages->isNotEmpty()) {
            return response()->json(
                ['message' => $stages],
                200
            );
        }
        return response()->json(
            ['message' => 'stage not found!'],
            404
        );
    }

    //  Store a new stage or add to the seeder if it doesn't exist
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
            'stage' => 'required|string'
        ]);

        $stageName = $request->stage;

        $existingStage = Stage::where('stage', $stageName)
            ->first();

        if (!$existingStage) {
            $newStage = Stage::create([
                'stage' => $stageName
            ]);

            return response()->json(
                ['message' => 'Stage added successfully', 'stage' => $newStage],
                201
            );
        } else {
            return response()->json(
                ['message' => 'Stage already exists'],
                200
            );
        }
    }

    //  update an existing stage
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
            'stage' => 'required|exists:stages,stage',
            'newStage' => 'required|string'
        ]);

        $stage = Stage::where('stage', $request->stage)
            ->first();
        $newStage = $request->newStage;

        $newStage = Stage::where('stage', $request->newStage)
            ->first();

        if ($newStage) {
            return response()->json(
                ['error' => 'stage alredy exists!'],
                404
            );
        }
        if ($stage->stage === $newStage) {
            return response()->json(
                ['error' => 'nothing to update!'],
                404
            );
        }

        $stage->stage = $request->newStage;
        $stage->save();

        return response()->json(
            ['message' => 'stage updated successfully'],
            200
        );
    }

    // delete a stage
    public function destroy(Request $request)
    {
        // $user = Auth::user();

        // if ($user->role != (1 || 2)) {
        //     return response()->json(
        //         ['error' => 'Unauthorized'],
        //         403
        //     );
        // }

        $stage = Stage::where('stage', $request->stage)
            ->first();
        if ($stage) {
            $stage->delete();
            return response()->json(
                ['message' => 'Successfully deleted stage!'],
                200
            );
        }
        return response()->json(
            ['message' => 'stage not found!'],
            404
        );
    }
}
