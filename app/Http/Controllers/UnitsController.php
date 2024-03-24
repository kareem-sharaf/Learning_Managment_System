<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Validator;
use App\Http\Requests\UnitRequest;


class UnitsController extends Controller
{
    //******************************************************************************************* */
    public function show_all_units(UnitRequest $request)
    {
        $input= $request->all();
        $subject_id = $input['subject_id'];
        $unit = Unit::where('subject_id', $input['subject_id'])->get();
        $message = "this is the all units";

        return response()->json([
            'message' => $message,
            'data' => $unit,
        ]);
    }
//************************************************************************************************************** */

    public function search_to_unit(UnitRequest $request)
    {
        $input = $request->all();
        $unit = Unit::where('name', 'like', '%' . $input['name'] . '%')
            ->where('subject_id', $input['subject_id'])
            ->get();

        if (is_null($unit)) {
            $message = "The unit doesn't exist.";
            return response()->json([
                'message' => $message,
            ]);
        }

        $message = "This is the unit.";
        return response()->json([
            'message' => $message,
            'data' => $unit,
        ]);
    }
//******************************************************************************************* */
    public function add_unit(UnitRequest $request)
    {
        $user = auth()->user();
        $input = $request->all();
        $unit = Unit::create($input);
        $message = "add unit successfully";
        return response()->json(
            [
                'message' => $message,
                'data' => $unit
            ]
        );
    }
//**************************************************************** */
   public function edit_unit(UnitRequest $request)
    {
        $user = auth()->user();
        $input = $request->all();

        $unit = Unit::where('id', $input['unit_id'])->first();
        $unit->name = $input['name'];
        // $unit->image = $input['image'];
        // $unit->video = $input['video'];
        $unit->name = $input['description'];
        $unit->save();

        $message = "The unit edit successfully.";
        return response()->json([
            'message' => $message,
            'data' => $unit
        ]);
    }
//********************************************************************************************************************************************* */
    public function delete_unit($unit_id)
    {
        $user = auth()->user();
        $unit = Unit::where('id', $unit_id)->first();
        if (is_null($unit)) {
            $message = "The unit doesn't exist.";
            return response()->json([
                'message' => $message,
            ]);
        }
        $unit->delete();
        $message = "The unit deleted successfully.";
        return response()->json([
            'message' => $message,
            'data' => $unit,
        ]);
    }
}
//******************************************************************************************************************************************* */
