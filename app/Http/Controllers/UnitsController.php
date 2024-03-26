<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitsController extends Controller
{
    //******************************************************************************************* */
    public function show_all_units(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required',
        ]);
        if ($validator->fails()) {
            return 'error in validation.';
        }

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

    public function search_to_unit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'subject_id' => 'required'
        ]);
        if ($validator->fails()) {
            return 'error in validation.';
        }
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
    public function add_unit(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            //  'image'=>'required',
            //  'video'=>'required',
            'subject_id' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return 'error in validation.';
        }

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
   public function edit_unit(Request $request)
    {
        $user = auth()->user();
        $input = $request->all();
        $validator = Validator::make($input, [
            'unit_id' => 'required',
            'name' => 'required',
            //  'image'=>'required',
            //  'video'=>'required',
            'description' => 'required'
        ]);
        $unit = Unit::where('id', $input['unit_id'])->first();
        if ($validator->fails()) {
            $message = "There is an error in the inputs.";
            return response()->json([
                'message' => $message,
                'data' => $input,
            ]);
        }
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
