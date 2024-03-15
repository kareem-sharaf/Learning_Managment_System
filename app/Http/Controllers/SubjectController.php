<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\TeachersController;

class SubjectController extends Controller
{

    public function show_all_subjects(Request $request)
    {
    $year_id = $request->all();
    $subject = Subject::where('year_id', $year_id)->get();
    $message = "this is the all subjects";

    return response()->json([
        'status' => '200',
        'message' => $message,
        'data' => $subject,
    ]);
    }







    public function search_to_subject(Request $request)
{
    $validator = Validator::make($request->all(),[
        'name'=>'required',
        'year_id'=>'required'
    ]);
    if ($validator->fails()) {
        return 'error in validation.';
    }
    $input = $request->all();
    $subject = subject::where('name', 'like', '%'.$input['name'].'%')
                         ->where('year_id', $input['year_id'])
                         ->get();
        if ($subject->isEmpty()) {
            $message = "The subject doesn't exist.";
            return response()->json([
            'status' => 0,
            'message' => $message,
            ]);
          }

    $message = "This is the subject.";
    return response()->json([
        'status' => 200,
        'message' => $message,
        'data' => $subject,
    ]);
}




public function add_subject_and_assign_teachers(Request $request)
{
    $user = auth()->user();
    // if($user->role == 2){

    $input = $request->all();

    $validator_subject = Validator::make($input, [
        'name' => 'required',
        // 'image' => 'required',
        // 'video' => 'required',
        'stage_id' => 'required',
        'year_id' => 'required'
    ]);

    $validator_content = Validator::make($input, [
        'content' => 'required|array',
        'content.*.teacher_id' => 'required|integer',
    ]);

    if ($validator_subject->fails() || $validator_content->fails()) {
        return 'Error in validation.';
    }

    $subject = Subject::create([
        'name' => $input['name'],
        'stage_id' => $input['stage_id'],
        'year_id' => $input['year_id']
    ]);

    foreach ($input['content'] as $item) {
        $teacher = Teacher::find($item['teacher_id']);

        if (!$teacher) {
            return response()->json([
                'status' => 0,
                'message' => 'Teacher with ID ' . $item['teacher_id'] . ' not found.'
            ]);
        }

        $teacher->subjects()->attach($subject->id);
    }

    $message = "Subject added and teachers assigned successfully.";
    return response()->json([
        'status' => '200',
        'message' => $message,
        'data' => $subject
    ]);

    // } else {
    //     $message = "You can't add the subject and assign teachers.";
    //     return response()->json([
    //         'status' => '500',
    //         'message' => $message
    //     ]);
    // }
}






public function edit_subject(Request $request,$subject_id)
{
    $user = auth()->user();
  //  if($user->role == 2){
    $subject = Subject::where('id', $subject_id)->first();
    $input = $request->all();
    $validator = Validator::make($input, [
        'name' => 'required',
       // 'image' => 'required',
        //'video' => 'required',
    ]);

    if ($validator->fails()) {
        $message = "There is an error in the inputs.";
        return response()->json([
            'status' => 0,
            'message' => $message,
            'data' => $input,
        ]);
    }
    $subject->name = $input['name'];
   // $subject->image = $input['image'];
   // $subject->video = $input['video'];
    $subject->save();

    $message = "The subject has been updated successfully.";
    return response()->json([
        'status' => 1,
        'message' => $message,
        'data' => $subject
    ]);

  /*  }else{
        $message="you can't edite subject ";
        return response()->json(
            [
                'status'=>'500',
                'message'=>$message
            ]
        );
    }*/

}




public function delete_subject($subject_id)
    {
        $user = auth()->user();
       // if($user->role == 2){
            $subject = Subject::where('id', $subject_id)->first();
            if (is_null($subject)) {
                $message = "The subject doesn't exist.";
                return response()->json([
                    'status' => '500',
                    'message' => $message,
                ]);
            }
            $subject->delete();
            $message = "The subject deleted successfully.";
             return response()->json([
            'status' => 1,
            'message' => $message,
            'data' => $subject,
        ]);
   /* }else{
        $message="you can't delete subject ";
        return response()->json(
            [
                'status'=>'500',
                'message'=>$message
            ]
        );
    }*/


}

}
