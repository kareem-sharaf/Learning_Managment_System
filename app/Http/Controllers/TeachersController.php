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


    //********************************************************************************************** */
    public function show_all_teachers()
    {










        $teacher = Teacher::get();
        $message = "this is the all teachers.";
        return response()->json([
            'message' => $message,
            'data' => $teacher,
        ]);
    }
    //********************************************************************************************** */
    public function show_one_teacher($teacher_id)
    {



        $teacher = Teacher::where('id', $teacher_id)->first();
        $message = "this is the teacher.";
        return response()->json([
            'message' => $message,
            'data' => $teacher,
        ]);
    }
    //********************************************************************************************** */
    public function show_year_teachers($year_id)
    {
        $teachers = Year::find($year_id)->teachers()->get();
        $message = "this is the teachers.";



        return response()->json([
            'message' => $message,
            'data' => $teachers,
        ]);



    }
    //********************************************************************************************** */
    public function search_to_teacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return 'error in validation.';
        }
        $input = $request->all();
        $teacher = Teacher::where('name', 'like', '%' . $input['name'] . '%')
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
        $input = $request->all();
        $validator_teacher = Validator::make($input, [
            'name' => 'required',
            // 'image_data' => 'required',
            'description' => 'required',
            'content' => 'required|array',
            'content.*.year_id' => 'required|integer',
            'content.*.subject_id' => 'required|integer'
        ]);
        if ($validator_teacher->fails()) {
            return 'Error in validation.';
        }

        $teacher = Teacher::create([
            'name' => $input['name'],
            // 'naimage_datame' => $input['image_data'],
            'description' => $input['description']
        ]);

        foreach ($input['content'] as $item) {
            $year = Year::where('id', $item['year_id'])->first();
            if (!$year) {
                return response()->json([
                    'message' => 'year with ID ' . $item['year_id'] . ' not found.'
                ]);
            }
            $subject = Subject::where('id', $item['subject_id'])->first();
            if (!$subject) {
                return response()->json([
                    'message' => 'subject with ID ' . $item['subject_id'] . ' not found.'
                ]);
            }
            $year->teachers()->attach($teacher->id);
            $subject->teachers()->attach($teacher->id);
        }

        $message = "tacher added successfully.";
        return response()->json([
            'message' => $message,
            'data' => $teacher
        ]);


    }













    public function edit_teacher(Request $request)
    {
        $user = auth()->user();
        $input = $request->all();


        $validator_teacher = Validator::make($input, [
            'teacher_id' => 'required',
            'name' => 'required',
            // 'image' => 'required'
        ]);

        $validator_subject = Validator::make($input, [
            'subject_content' => 'array',
            'subject_content.*.subject_id' => 'integer',
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

    }








    public function delete_teacher($teacher_id)
    {
        $user = auth()->user();



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



    }
}
