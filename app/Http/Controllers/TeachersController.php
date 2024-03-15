<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Validator;

class TeachersController extends Controller
{
    public function show_all_teachers(Request $request)
{

    $input = $request->all();
    $teachers=Teacher::get();
    if ($teachers->isEmpty()) {
        $message = "No teachers found based on the given criteria.";
        return response()->json([
            'status' => 0,
            'message' => $message,
        ]);
    }

    $message = "These are the teachers based on the search criteria.";
    return response()->json([
        'status' => 200,
        'message' => $message,
        'data' => $teachers,
    ]);
}




    public function search_to_teacher(Request $request)
{
    $validator = Validator::make($request->all(),[
        'name'=>'required'
    ]);
    if ($validator->fails()) {
        return 'error in validation.';
    }
    $input = $request->all();
    $teacher = Teacher::where('name', 'like', '%'.$input['name'].'%')
                  ->get();

        if (is_null($teacher)) {
            $message = "The teacher doesn't exist.";
            return response()->json([
            'status' => 0,
            'message' => $message,
            ]);
          }

    $message = "This is the teacher.";
    return response()->json([
        'status' => 200,
        'message' => $message,
        'data' => $teacher,
    ]);
}






public function add_teacher(Request $request)
    {
        $user = auth()->user();
    //    if($user->role == 2){
             $validator = Validator::make($request->all(),[
                'name'=>'required',
             //  'image'=>'required',
            ]);
            if ($validator->fails()) {
                return 'error in validation.';
            }
            $input = $request->all();
            $teacher = Teacher::create($input);
            $message="add teacher successfully";
            return response()->json(
                [
                'status'=>'200',
                'message'=>$message,
                'data'=>$teacher
                ]
                );
        //}
     /*   else{
            $message="you can't add teacher ";
            return response()->json(
                [
                    'status'=>'500',
                    'message'=>$message
                ]
            );
        }*/

}

}
