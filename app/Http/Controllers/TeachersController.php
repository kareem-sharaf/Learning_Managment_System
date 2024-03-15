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
        'status' => '200',
        'message' => $message,
        'data' => $teacher,
    ]);
    }

    public function show_one_teacher($teacher_id)
    {
    $teacher = Teacher::where('id',$teacher_id)->first();
    $message = "this is the teacher.";

    return response()->json([
        'status' => '200',
        'message' => $message,
        'data' => $teacher,
    ]);
    }
    public function show_stage_teachers($stage_id)
    {
        $teachers = Stage::find($stage_id)->teachers()->get();
        $message = "this is the teachers.";
        return response()->json([
            'status' => '200',
            'message' => $message,
            'data' => $teacher,
        ]);
    }
public function show_year_teachers($year_id)
    {
        $teachers = Year::find($year_id)->teachers()->get();
        $message = "this is the teachers.";
        return response()->json([
            'status' => '200',
            'message' => $message,
            'data' => $teacher,
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






public function add_teacher_and_assign_subjects(Request $request)
{
    $user = auth()->user();
    // if($user->role == 2){

    $input = $request->all();

    $validator_teacher = Validator::make($input, [
        'name' => 'required',
        // 'image' => 'required',
    ]);

    $validator_content = Validator::make($input, [
        'content' => 'required|array',
        'content.*.subject_id' => 'required|integer',
    ]);

    if ($validator_teacher->fails() || $validator_content->fails()) {
        return 'Error in validation.';
    }

    $teacher = Teacher::create([
        'name' => $input['name'],
    ]);

    foreach ($input['content'] as $item) {
        $subject = Subject::find($item['subject_id']);

        if (!$subject) {
            return response()->json([
                'status' => 0,
                'message' => 'subject with ID ' . $item['subject_id'] . ' not found.'
            ]);
        }

        $subject->teachers()->attach($teacher->id);
    }

    $message = "tacher added and subjects assigned successfully.";
    return response()->json([
        'status' => '200',
        'message' => $message,
        'data' => $teacher
    ]);

    // } else {
    //     $message = "You can't add the teacher and assign teachers.";
    //     return response()->json([
    //         'status' => '500',
    //         'message' => $message
    //     ]);
    // }
}










public function edit_teacher(Request $request, $teacher_id)
{
    $user = auth()->user();

// if($user->role == 2){
    $teacher = Teacher::find($teacher_id);

    if (!$teacher) {
        return response()->json([
            'status' => 0,
            'message' => 'teacher not found.'
        ]);
    }

    $input = $request->all();

    $validator_teacher = Validator::make($input, [
        'name' => 'required',
        // 'image' => 'required'
    ]);

    $validator_content = Validator::make($input, [
        'content' => 'required|array',
        'content.*.subject_id' => 'required|integer',
    ]);

    if ($validator_teacher->fails() || $validator_content->fails()) {
        return 'Error in validation.';
    }

    $teacher->update([
        'name' => $input['name'],
        // 'image' => $input['image']
    ]);

    $teacher->subjects()->sync($input['content']);

    $message = "The teacher has been updated successfully.";
    return response()->json([
        'status' => 1,
        'message' => $message,
        'data' => $teacher
    ]);
    // } else {
    //     $message = "You can't add the teacher and assign teachers.";
    //     return response()->json([
    //         'status' => '500',
    //         'message' => $message
    //     ]);
    // }
}






public function delete_teacher($teacher_id)
{
    $user = auth()->user();
// if($user->role == 2){


    $teacher = Teacher::find($teacher_id);

    if (!$teacher) {
        $message = "The teacher doesn't exist.";
        return response()->json([
            'status' => 500,
            'message' => $message,
        ]);
    }

    $teacher->subjects()->detach();

    $teacher->delete();

    $message = "The teacher deleted successfully.";
    return response()->json([
        'status' => 1,
        'message' => $message,
    ]);
    // } else {
        //     $message = "You can't add the teacher and assign teachers.";
        //     return response()->json([
        //         'status' => '500',
        //         'message' => $message
        //     ]);
        // }
}
}
