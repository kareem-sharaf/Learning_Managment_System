<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Stage;
use App\Models\Year;
use App\Models\SubjectYear;
use App\Models\TeacherSubjectYear;


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
            'name' => 'required',
            'year_id' => 'required'
        ]);
        if ($validator->fails()) {
            return 'error in validation.';
        }
        $input = $request->all();
        $teacher = Teacher::where('name', 'like', '%' . $input['name'] . '%')
                    ->whereHas('years', function ($query) use ($input) {
                        $query->where('year_id', $input['year_id']);
                    })->get();

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
//****************************************************************************************************** */
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
            $year = Year::find($item['year_id']);
   if (!$year) {
       return response()->json([
           'message' => 'Year with ID ' . $item['year_id'] . ' not found.'
       ]);
   } else {
       $subject = Subject::find($item['subject_id']);
       if (!$subject) {
           return response()->json([
               'message' => 'Subject with ID ' . $item['subject_id'] . ' not found.'
           ]);
       } else {
           $subjectYear = SubjectYear::where('year_id', $year->id)->where('subject_id', $subject->id)->first();
           if ($subjectYear) {
               $teacher->subjectYears()->attach($subjectYear->id);
           } else {
               return response()->json([
                   'message' => 'Subject Year not found.'
               ]);
           }
       }
   }
        }
        $message = "teacher added successfully.";
        return response()->json([
            'message' => $message,
            'data' => $teacher
        ]);
    }
//********************************************************************************************************** */
    public function edit_teacher(Request $request)
    {
        $user = auth()->user();
        $input = $request->all();


        $validator_teacher = Validator::make($input, [
            'teacher_id' => 'required|integer',
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

        $teacher = Teacher::find($input['teacher_id']);

        if (!$teacher) {
            return response()->json([
                'message' => 'teacher not found.'
            ]);
        }


        $teacher->update([
            'name' => $input['name'],
            // 'image' => $input['image'],
            'description' => $input['description']
        ]);

        $teacher->subjectYears()->detach(); // قم بفصل جميع الروابط الحالية

        foreach ($input['content'] as $item) {
            $year = Year::find($item['year_id']);
   if (!$year) {
       return response()->json([
           'message' => 'Year with ID ' . $item['year_id'] . ' not found.'
       ]);
   } else {
       $subject = Subject::find($item['subject_id']);
       if (!$subject) {
           return response()->json([
               'message' => 'Subject with ID ' . $item['subject_id'] . ' not found.'
           ]);
       }else{
        $subjectYear = SubjectYear::where('year_id', $year->id)->where('subject_id', $subject->id)->first();
        if ($subjectYear) {
            $teacher->subjectYears()->attach($subjectYear->id);
        } else {
            return response()->json([
                'message' => 'Subject Year not found.'
            ]);
        }
       }
    }
}
    $message = "The teacher update successfully.";
    return response()->json([
        'message' => $message,
        'data' => $teacher
    ]);
}
//*************************************************************************************************** */
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

        $teacher->subjectYears()->detach();
        $teacher->delete();

        $message = "The teacher deleted successfully.";
        return response()->json([
            'message' => $message,
        ]);
    }
}
//******************************************************************************************************** */
