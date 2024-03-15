<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Validator;

class UnitsController extends Controller
{
    public function show_all_units(Request $request)
    {
        $input = $request->all();
        $unit = Unit::where('subject_id', $input['subject_id'])->get();
        $message = "this is the all units";

    return response()->json([
        'status' => '200',
        'message' => $message,
        'data' => $unit,
    ]);
    }






    public function search_to_unit(Request $request)
{
    $validator = Validator::make($request->all(),[
        'name'=>'required',
        'subject_id'=>'required'
    ]);
    if ($validator->fails()) {
        return 'error in validation.';
    }
    $input = $request->all();
    $unit = Unit::where('name', 'like', '%'.$input['name'].'%')
                  ->where('subject_id', $input['subject_id'])
                  ->get();

        if (is_null($unit)) {
            $message = "The unit doesn't exist.";
            return response()->json([
            'status' => 0,
            'message' => $message,
            ]);
          }

    $message = "This is the unit.";
    return response()->json([
        'status' => 200,
        'message' => $message,
        'data' => $unit,
    ]);
}




public function add_unit(Request $request)
    {
        $user = auth()->user();
    //    if($user->role == 2){
             $validator = Validator::make($request->all(),[
                'name'=>'required',
             //  'image'=>'required',
              //  'video'=>'required',
              'price'=>'required',
              'subject_id'=>'required',
             // 'content'=>'required|array',
              //'content.*.lesson_id'=>'required|integer',
            ]);
            if ($validator->fails()) {
                return 'error in validation.';
            }
            $input = $request->all();
            $unit = Unit::create($input);
            $message="add unit successfully";
            return response()->json(
                [
                'status'=>'200',
                'message'=>$message,
                'data'=>$unit
                ]
                );
        //}
     /*   else{
            $message="you can't add unit ";
            return response()->json(
                [
                    'status'=>'500',
                    'message'=>$message
                ]
            );
        }*/

}





public function edit_unit(Request $request,$unit_id)
{
    $user = auth()->user();
  //  if($user->role == 2){
    $unit = Unit::where('id', $unit_id)->first();
    $input = $request->all();
    $validator = Validator::make($input, [
        'name'=>'required',
             //  'image'=>'required',
              //  'video'=>'required',
              'price'=>'required',
              'subject_id'=>'required',
             // 'content'=>'required|array',
              //'content.*.lesson_id'=>'required|integer',
    ]);

    if ($validator->fails()) {
        $message = "There is an error in the inputs.";
        return response()->json([
            'status' => 0,
            'message' => $message,
            'data' => $input,
        ]);
    }
    $unit->name = $input['name'];
   // $unit->image = $input['image'];
   // $unit->video = $input['video'];
   $unit->price = $input['price'];
   $unit->content = $input['content'];
    $unit->save();

    $message = "The unit has been updated successfully.";
    return response()->json([
        'status' => 1,
        'message' => $message,
        'data' => $unit
    ]);

  /*  }else{
        $message="you can't edite unit ";
        return response()->json(
            [
                'status'=>'500',
                'message'=>$message
            ]
        );
    }*/

}






public function delete_unit($unit_id)
    {
        $user = auth()->user();
       // if($user->role == 2){
            $unit = Unit::where('id', $unit_id)->first();
            if (is_null($unit)){
                $message = "The unit doesn't exist.";
                return response()->json([
                'status' => 0,
                'message' => $message,
                ]);
              }
            $unit->delete();
            $message = "The unit deleted successfully.";
             return response()->json([
            'status' => 1,
            'message' => $message,
            'data' => $unit,
        ]);
   /* }else{
        $message="you can't delete unit ";
        return response()->json(
            [
                'status'=>'500',
                'message'=>$message
            ]
        );
    }*/


}

}
