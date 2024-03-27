<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Year;
use App\Models\Stage;

use App\Http\Requests\SubjectRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TeachersController;

class SubjectController extends Controller
{

//**********************************************************************************************
  public function show_all_subjects(Request $request)
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
            'year_id' => 'required|string',
            'name' => 'required|string'
        ]);
        $year_id = $request->year_id;
        $name = $request->name;
        $subject = subject::where('name', 'like', '%' . $name . '%')
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
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            // 'image_data' => 'required',
            // 'video_id' => 'required',
            // 'file_id' => 'required',
            'years_content' => 'required',
            'years_content.*.year_id' => 'required|integer',
        ]);

        $subject = Subject::create([
            'name' => $request->name,
            'price	' => $request->price,
            //'image_data	' => $request->image_data,
            //'video_id	' => $request->video_id,
            //'file_id	' => $request->file_id,
        ]);

        foreach ($request->years_content as $item) {
            $year = Year::find($item['year_id']);

            if (!$year) {
                return response()->json([
                    'message' => 'year with ID ' . $item['year_id'] . ' not found.'
                ]);
            }
            $year->subjects()->attach($subject->id);
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
            'subject_id' => 'required',
            'name' => 'required',
            'price' => 'required',
            // 'image_data' => 'required',
            // 'video_id' => 'required',
            // 'file_id' => 'required',
            'years_content' => 'required',
            'years_content.*.year_id' => 'required|integer',
        ]);

        $subject = Subject::find($request->subject_id);
        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found.'
            ]);
        }
        $subject->update([
            'name' => $request->name,
            'price	' => $request->price,
            //'image_data	' => $request->image_data,
            //'video_id	' => $request->video_id,
            //'file_id	' => $request->file_id,
        ]);

        $subject->years()->detach();
        $subject->years()->syncWithoutDetaching($request->years_content);

        $message = "The subject edit successfully.";
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

        $subject->years()->detach();
        $subject->delete();

        $message = "The subject deleted successfully.";
        return response()->json([
            'message' => $message,
        ]);

    }
}
    //***********************************************************************************************************************\\
