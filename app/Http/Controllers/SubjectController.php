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

    //**********************************************************************************************
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
        $request->validate([
            'class_id' => 'required|string',
            'year_id',
            'name' => 'required|string'
        ]);
        $class_id = $request->class_id;
        $year_id = $request->year_id;
        $name = $request->name;
        if($class_id==1){//if the class is educational
            $subject = subject::where('name', 'like', '%' . $name . '%')
            ->whereHas('years', function($q) use ($year_id) {
                $q->where('year_id', $year_id);
            })->get();
        }else{
        $subject = subject::where('name', 'like', '%' . $name . '%')
            ->where('class_id', $class_id)
            ->get();
        }
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
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'class_id' => 'required',
            // 'image_data' => 'required',
            // 'video_id' => 'required',
            // 'file_id' => 'required',
            'years_content' => 'required',
            'years_content.*.year_id' => 'required|integer',
            'teachers_content',
            'teachers_content.*.teacher_id' => 'required|integer',
        ]);

        $subject = Subject::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'class_id' => $request->class_id,
            //'image_data' => $request->image_data,
            //'video_id' => $request->video_id,
            //'file_id' => $request->file_id,
        ]);

            foreach ($request->years_content as $yearItem) {
                $year = Year::find($yearItem['year_id']);
                if (!$year) {
                    return response()->json([
                        'message' => 'year with ID ' . $yearItem['year_id'] . ' not found.'
                    ]);
                }
                $subject->years()->attach($year->id);

                foreach ($request->teachers_content as $item) {
                    $teacher = Teacher::find($item['teacher_id']);
                    if (!$teacher) {
                        return response()->json([
                            'message' => 'teacher with ID ' . $item['teacher_id'] . ' not found.'
                        ]);
                    }

                    $subjectYear = SubjectYear::where('year_id', $year->id)
                                            ->where('subject_id', $subject->id)->first();
                    if ($subjectYear) {
                       $teacher->subjectYears()->attach($subjectYear->id);
                      } else {
                   return response()->json([
                       'message' => 'Subject Year not found.'
                   ]);
               }
                }
            }
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
        $request->validate([
            "subject_id"=>"required",
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            // 'image_data' => 'required',
            // 'video_id' => 'required',
            // 'file_id' => 'required',
            'years_content' => 'required',
            'years_content.*.year_id' => 'required|integer',
            'teachers_content' => 'required',
            'teachers_content.*.teacher_id' => 'required|integer',
        ]);

        $subject = Subject::find($request->subject_id);


        $subject->years()->detach(); // قم بفصل جميع الروابط الحالية
        $subject->subjectYears()->detach();

        foreach ($input['years_content'] as $item) {
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



        // if (!$subject) {
        //     return response()->json([
        //         'message' => 'Subject not found.'
        //     ]);
        // }
        // $subject->update([
        //     'name' => $request->name,
        //     'price' => $request->price,
        //     'description' => $request->description,
        //     // 'image_data' => $request->image_data,
        //     // 'video_id' => $request->video_id,
        //     // 'file_id' => $request->file_id,
        // ]);




        // $subject->years()->detach();
        // $subject->years()->sync($request->years_content);

        // $subject->subjectYears()->detach();
        // $subject->subjectYears()->sync($request->teachers_content);

        // $message = "The subject edit was successful.";
        // return response()->json([
        //     'message' => $message,
        //     'data' => $subject
        // ]);
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

        $subject->years()->detach();
        $subject->delete();

        $message = "The subject deleted successfully.";
        return response()->json([
            'message' => $message,
        ]);

    }
}
    //***********************************************************************************************************************\\
