<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Stage;
use App\Models\Year;

use Illuminate\Http\Request;
use Validator;

class TeachersController extends Controller
{



    public function show_all_teachers()
    {
    $teacher = Teacher::get();
    $message = "this is the all teachers.";

    return response()->json([
        'message' => $message,
        'data' => $teacher,
    ]);
    }





    public function show_one_teacher($teacher_id)
    {
    $teacher = Teacher::where('id',$teacher_id)->first();
    $message = "this is the teacher.";

    return response()->json([
        'message' => $message,
        'data' => $teacher,
    ]);
    }








        public function show_year_teachers($year_id)
    {
        $teachers = Year::find($year_id)->teachers()->get();
        $message = "this is the teachers.";
        return response()->json([
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
            'message' => $message,
            ]);
          }

    $message = "This is the teacher.";
    return response()->json([
        'message' => $message,
        'data' => $teacher,
    ]);
}









public function add_teacher(Request $request)
{
    $user = auth()->user();
     if($user->role_id == 2){

    $input = $request->all();

    $validator_teacher = Validator::make($input, [
        'name' => 'required',
        // 'image' => 'required',
    ]);

    $validator_subject = Validator::make($input, [
        'subject_content' => 'required|array',
        'subject_content.*.subject_id' => 'required|integer',
    ]);
    $validator_year = Validator::make($input, [
        'year_content' => 'required|array',
        'year_content.*.year_id' => 'required|integer',
    ]);
    if ($validator_teacher->fails() || $validator_subject->fails() || $validator_year->fails()) {
        return 'Error in validation.';
    }

    $teacher = Teacher::create([
        'name' => $input['name'],
    ]);

    foreach ($input['subject_content'] as $item) {
        $subject = Subject::find($item['subject_id']);

        if (!$subject) {
            return response()->json([
                'message' => 'subject with ID ' . $item['subject_id'] . ' not found.'
            ]);
        }

        $subject->teachers()->attach($teacher->id);
    }
    foreach ($input['year_content'] as $item) {
        $year = Year::find($item['year_id']);

        if (!$year) {
            return response()->json([
                'message' => 'year with ID ' . $item['year_id'] . ' not found.'
            ]);
        }

        $year->teachers()->attach($teacher->id);
    }
    $message = "tacher added successfully.";
    return response()->json([
        'message' => $message,
        'data' => $teacher
    ]);

     } else {
         $message = "You can't add the teacher.";
         return response()->json([
             'message' => $message
         ]);
     }
}










public function edit_teacher(Request $request)
{
    $user = auth()->user();
    $input = $request->all();

 if($user->role_id == 2){
    $validator_teacher = Validator::make($input, [
        'teacher_id' =>'required',
        'name' => 'required',
        // 'image' => 'required'
    ]);

    $validator_subject = Validator::make($input, [
        'subject_content' => 'required|array',
        'subject_content.*.subject_id' => 'required|integer',
    ]);
    $validator_year = Validator::make($input, [
        'year_content' => 'required|array',
        'year_content.*.year_id' => 'required|integer',
    ]);
    if ($validator_teacher->fails() || $validator_subject->fails() || $validator_year->fails()) {
        return 'Error in validation.';
    }

    $teacher = Teacher::find($input['teacher_id']);

    if (!$teacher) {
        return response()->json([
            'message' => 'teacher not found.'
        ]);
    }


    $teacher->update([
        'name' => $input['name'],
        // 'image' => $input['image']
    ]);

    $teacher->subjects()->sync($input['subject_content']);
    $teacher->years()->sync($input['year_content']);

    $message = "The teacher has been updated successfully.";
    return response()->json([
        'message' => $message,
        'data' => $teacher
    ]);
     } else {
         $message = "You can't edite the teacher.";
         return response()->json([
             'message' => $message
         ]);
     }
}








public function delete_teacher($teacher_id)
{
    $user = auth()->user();
 if($user->role_id == 2){


    $teacher = Teacher::find($teacher_id);

    if (!$teacher) {
        $message = "The teacher doesn't exist.";
        return response()->json([
            'message' => $message,
        ]);
    }

    $teacher->subjects()->detach();
    $teacher->years()->detach();

    $teacher->delete();

    $message = "The teacher deleted successfully.";
    return response()->json([
        'message' => $message,
    ]);
     } else {
             $message = "You can't delete the teacher.";
             return response()->json([
                 'message' => $message
             ]);
         }
}
}
