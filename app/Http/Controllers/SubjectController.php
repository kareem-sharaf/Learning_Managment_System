<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Year;
use App\Models\Stage;
use App\Models\SubjectYear;


use App\Http\Requests\SubjectRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TeachersController;

class SubjectController extends Controller
{

    //**********************************************************************************************\/
    //show all subject in the class
  public function show_all_subjects(Request $request)
  {
      $request->validate([
          'class_id' => 'required|integer'
      ]);
      $class_id = $request->class_id;
      $subject = Subject::where('class_id', $class_id)
      ->get();
      $message = "this is the all subjects in the class.";

      return response()->json([
          'message' => $message,
          'data' => $subject
      ]);
  }
//**********************************************************************************************
//show all subjects in the class education
  public function all_subjects_in_year(Request $request)
    {
        $request->validate([
            'year_id' => 'required|integer'
        ]);
        $year_id = $request->year_id;
        $subject = Subject::whereHas('teachers', function($q) use ($year_id) {
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
    $request->validate([
        'class_id' => 'required|string',
        'year_id' => 'integer',
        'name' => 'required|string',
    ]);

    $class_id = $request->class_id;
    $year_id = $request->year_id;
    $name = $request->name;

    if ($class_id == 1) { // if the class is educational
        $subjects = Subject::where('name', 'like', '%' . $name . '%')
            ->whereHas('teachers', function ($q) use ($year_id) {
                $q->where('year_id', $year_id);
            })->get();
    } else {
        $subjects = Subject::where('name', 'like', '%' . $name . '%')
            ->where('class_id', $class_id)
            ->get();
    }

    if ($subjects->isEmpty()) {
        $message = "subject does not exist.";
        return response()->json([
            'message' => $message,
        ]);
    }

    return response()->json([
        'message' => " this is the subjects .",
        'data' => $subjects,
    ]);
}

    //***********************************************************************************************************************\\
    public function add_subject(Request $request)
{
    $user = auth()->user();
    $request->validate([
        'class_id' => 'required',
        'name' => 'required',
        'price' => 'required',
        'description' => 'required',
        'teachers_content' => 'required|array',
        'teachers_content.*.teacher_id' => 'required|integer',
        'years_content.*.year_id' => 'integer',
    ]);

    $subject = Subject::create([
        'name' => $request->name,
        'price' => $request->price,
        'description' => $request->description,
        'class_id' => $request->class_id,
    ]);

    if ($request->class_id == 1) {//if the class is educational
        $yearsContent = $request->years_content;
        $teachersContent = $request->teachers_content;

        foreach ($teachersContent as $teacher) {
            foreach ($yearsContent as $year) {
                $subject->teachers()->attach($teacher['teacher_id'], ['year_id' => $year['year_id']]);
            }
        }
    } else {
        foreach ($request->teachers_content as $teacher) {
            $subject->teachers()->attach($teacher['teacher_id']);
        }
    }

    return response()->json([
        'message' => 'Subject added successfully.',
        'data' => $subject,
    ]);
}

    //***********************************************************************************************************************\\
    public function edit_subject(Request $request)
    {
        $request->validate([
            'subject_id' => 'required',
            'class_id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'teachers_content' => 'required|array',
            'teachers_content.*.teacher_id' => 'required|integer',
            'years_content.*.year_id' => 'integer',
        ]);

        $subject_id = $request->subject_id;
        $subject = Subject::find($subject_id);

        $subject->name = $request->name;
        $subject->price = $request->price;
        $subject->description = $request->description;
        $subject->class_id = $request->class_id;
        $subject->save();


        if ($request->class_id == 1) { // if the class is educational
            $yearsContent = $request->years_content;
            $teachersContent = $request->teachers_content;

            $subject->teachers()->detach();

            foreach ($teachersContent as $teacher) {
                foreach ($yearsContent as $year) {
                    $subject->teachers()->attach($teacher['teacher_id'], ['year_id' => $year['year_id']]);
                }
            }
        } else {
            $subject->teachers()->detach();

            foreach ($request->teachers_content as $teacher) {
                $subject->teachers()->attach($teacher['teacher_id']);
            }
        }

        return response()->json([
            'message' => 'subject updated successfully',
            'data' => $subject,
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
        $subject->delete();

        $message = "The subject deleted successfully.";
        return response()->json([
            'message' => $message,
        ]);

    }
}
    //***********************************************************************************************************************\\
