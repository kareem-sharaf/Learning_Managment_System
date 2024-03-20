<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Year;
use App\Models\Stage;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\TeachersController;

class SubjectController extends Controller
{


  public function show_all_subjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year_id' => 'required',
        ]);
        if ($validator->fails()) {
            return 'error in validation.';
        }


        $input= $request->all();
        $year_id = $input['year_id'];
        $subject = Subject::whereHas('years', function($q) use ($year_id) {
            $q->where('year_id', $year_id);
        })->get();
        $message = "this is the all subjects";

        return response()->json([
            'message' => $message,
            'data' => $subject
        ]);
    }
    //***********************************************************************************************************************\\
    public function search_to_subject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'year_id' => 'required',
        ]);
        if ($validator->fails()) {
            return 'error in validation.';
        }

        $input = $request->all();
        $year_id = $input['year_id'];
        $subject = subject::where('name', 'like', '%' . $input['name'] . '%')
            ->whereHas('years', function($q) use ($year_id) {
                $q->where('year_id', $year_id);
            })->get();
        if ($subject->isEmpty()) {
            $message = "The subject doesn't exist.";
            return response()->json([
                'message' => $message,
            ]);
        }

        $message = "This is the subject.";
        return response()->json([
            'message' => $message,
            'data' => $subject,
        ]);
    }
    //***********************************************************************************************************************\\
    public function add_subject(Request $request)
    {
        $user = auth()->user();
        $input = $request->all();
        $validator_subject = Validator::make($input, [
            'name' => 'required',
      //      'image_data' => 'required',
        ]);

        $validator_year = Validator::make($input, [
            'years_content' => 'required|array',
            'years_content.*.year_id' => 'required|integer',
        ]);

        // $validator_teacher = Validator::make($input, [
        //     'teachers_content' => 'array',
        //     'teachers_content.*.teacher_id' => 'integer',
        // ]);
        if ($validator_subject->fails() || $validator_year->fails()/* || $validator_teacher->fails()*/) {
            return 'Error in validation.';
        }

        $subject = Subject::create([
            'name' => $input['name'],
           // 'image_data	' => $input['image_data	']
        ]);



        foreach ($input['years_content'] as $item) {
            $year = Year::find($item['year_id']);

            if (!$year) {
                return response()->json([
                    'message' => 'year with ID ' . $item['year_id'] . ' not found.'
                ]);
            }

            $year->subjects()->attach($subject->id);
        }
        // foreach ($input['teachers_content'] as $item) {
        //     $teacher = Teacher::find($item['teacher_id']);

        //     if (!$teacher) {
        //         return response()->json([
        //             'message' => 'teacher with ID ' . $item['teacher_id'] . ' not found.'
        //         ]);
        //     }

        //     $teacher->subjects()->attach($subject->id);
        // }

        $message = "Subject added successfully.";
        return response()->json([
            'message' => $message,
            'data' => $subject
        ]);

    }
    //***********************************************************************************************************************\\

public function edit_subject(Request $request)
    {
        $user = auth()->user();
        $input = $request->all();

        $validator_subject = Validator::make($input, [
            'subject_id'=>'required',
            'name' => 'required',
            // 'image' => 'required',
        ]);

        $validator_teacher = Validator::make($input, [
            'teacher_content' => 'array',
            'teacher_content.*.teacher_id' => 'integer',
        ]);
        $validator_year = Validator::make($input, [
            'year_content' => 'required|array',
            'year_content.*.year_id' => 'required|integer',
        ]);
        if ($validator_subject->fails() || $validator_teacher->fails() || $validator_year->fails()) {
            return 'Error in validation.';
        }
        $subject = Subject::find($input['subject_id']);
        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found.'
            ]);
        }


        $subject->update([
            'name' => $input['name'],
            // 'image' => $input['image'],
        ]);

        $subject->teachers()->sync($input['teacher_content']);
        $subject->years()->sync($input['year_content']);

        $message = "The subject has been updated successfully.";
        return response()->json([
            'message' => $message,
            'data' => $subject
        ]);

    }
    //***********************************************************************************************************************\\
 public function delete_subject($subject_id)
    {
        $user = auth()->user();
        $subject = Subject::find($subject_id);
        if (!$subject) {
            $message = "The subject doesn't exist.";
            return response()->json([
                'message' => $message,
            ]);
        }

        $subject->teachers()->detach();
        $subject->years()->detach();

        $subject->delete();

        $message = "The subject deleted successfully.";
        return response()->json([
            'message' => $message,
        ]);

    }
}
    //***********************************************************************************************************************\\
