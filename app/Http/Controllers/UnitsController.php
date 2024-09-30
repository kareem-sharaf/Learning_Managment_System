<?php

namespace App\Http\Controllers;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Year;
use App\Models\Stage;
use App\Models\SubjectYear;
use App\Models\User;
use App\Models\TeacherSubjectYear;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\Unit;
use App\Models\Subscription;
use App\Models\Video;
use App\Models\File;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\File;

use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use Illuminate\Support\Facades\Auth;

class UnitsController extends Controller
{
    //******************************************************************************************* */
    public function show_all_units(Request $request)
{
    $user = Auth::user();

    $user_id = $user->id;
    $role_id = $user->role_id;
    $subject_id = $request->subject_id;

    if (!$subject_id) {
        return response()->json(['error' => 'Subject ID is required'], 400);
    }

    $unit = Unit::where('subject_id', $subject_id)->where('exist',true)
        ->get();

        return response()->json([
            'message' => 'This is all units',
            'data' => $unit
        ]);
}
//************************************************************************************************************** */
    public function search_to_unit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'subject_id' => 'required'
        ]);
        if ($validator->fails()) {
            return 'error in validation.';
        }
        $input = $request->all();
        $unit = Unit::where('name', 'like', '%' . $input['name'] . '%')
            ->where('subject_id', $input['subject_id'])
            ->get();

        if (is_null($unit)) {
            $message = "The unit doesn't exist.";
            return response()->json([
                'message' => $message,
            ]);
        }

        $message = "This is the unit.";
        return response()->json([
            'message' => $message,
            'data' => $unit,
        ]);
    }
//******************************************************************************************* */
public function add_unit(Request $request)
{
    $user = Auth::user();
    $user_id = $user->id;

    $request->validate([
        'name' => 'required',
        'description' => 'required',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        'video_id' => 'integer|nullable',
        'file_id' => 'integer|nullable',
        'subject_id' => 'required|integer|exists:subjects,id',
    ]);

    $subject = Subject::find($request->input('subject_id'));
    if (!$subject) {
        return response()->json(['message' => 'subject not found.'], 404);
    }

    $SubjectTeacher = TeacherSubjectYear::where('user_id', $user_id)
                                        ->where('subject_id', $subject->id)
                                        ->first();
    if (!$SubjectTeacher) {
        return response()->json(['message' => 'you can not add this unit.'], 404);
    }

    $imageUrl = $request->file('image');
    $newName = time() . '.' . $imageUrl->getClientOriginalExtension();
    $imageUrl->move(public_path('unit_images'), $newName);
    $imageUrl = url('unit_images/' . $newName);

    $unit = Unit::create([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $imageUrl,
        'video_id' => $request->video_id,
        'file_id' => $request->file_id,
        'subject_id' => $request->subject_id,
    ]);

    if ($request->hasFile('video')) {
        $videoPath = $request->file('video')->store('videos', 'public');
        $video = new Video();
        $video->video = Storage::url($videoPath);
        $video->name = $request->video_name;
        $video->unit_id = $unit->id;
        $video->save();

        $unit->video_id = $video->id;
        $unit->save();
    }

    return response()->json([
        'message' => 'Unit added successfully',
        'data' => $unit,
    ]);
}

//**************************************************************** */
public function edit_unit(Request $request)
{
    $user = Auth::user();
    $user_id = $user->id;

    // التحقق من صحة البيانات
    $request->validate([
        'unit_id' => 'required|integer|exists:units,id',
        'name' => 'string|max:255|nullable',
        'image' => 'image|mimes:jpeg,png,jpg,gif|max:10240|nullable',
        'description' => 'string|nullable',
    ]);

    // إيجاد الوحدة
    $unit = Unit::find($request->unit_id);
    if (!$unit) {
        return response()->json(['message' => 'unit not found'], 404);
    }

    // التحقق من الصلاحية
    $SubjectTeacher = TeacherSubjectYear::where('user_id', $user_id)
                                        ->where('subject_id', $unit->subject_id)
                                        ->first();
    if (!$SubjectTeacher) {
        return response()->json(['message' => 'you can not edit this unit.'], 404);
    }

    // تحديث بيانات الوحدة
    $unitData = $request->only(['name', 'description', 'video_id', 'file_id']);

    // التحقق من وجود صورة جديدة وتحديثها
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة إذا كانت موجودة
        if ($unit->image) {
            $oldImagePath = str_replace('/storage', 'public', $unit->image);
            if (Storage::exists($oldImagePath)) {
                Storage::delete($oldImagePath);
            }
        }

        // حفظ الصورة الجديدة
        $imageUrl = $request->file('image');
        $newName = time() . '.' . $imageUrl->getClientOriginalExtension();
        $imageUrl->move(public_path('unit_images'), $newName);
        $unitData['image'] = url('unit_images/' . $newName);
    }

    // التحقق من وجود فيديو جديد وتحديثه
    if ($request->hasFile('video')) {
        $video = Video::find($unit->video_id);
        if ($video) {
            // حذف الفيديو القديم إذا كان موجودًا
            $oldVideoPath = str_replace('/storage', 'public', $video->video);
            if (Storage::exists($oldVideoPath)) {
                Storage::delete($oldVideoPath);
            }
        } else {
            // إنشاء فيديو جديد إذا لم يكن موجودًا
            $video = new Video();
            $video->unit_id = $unit->id;
        }

        // حفظ الفيديو الجديد
        $videoPath = $request->file('video')->store('videos', 'public');
        $video->video = Storage::url($videoPath);

        if ($request->filled('video_name')) {
            $video->name = $request->video_name;
        }

        $video->save();
        $unit->video_id = $video->id;
    }

    // تحديث بيانات الوحدة
    $unit->update($unitData);

    // إرجاع الرد
    return response()->json([
        'message' => 'Unit updated successfully',
        'data' => $unit,
    ]);
}


//********************************************************************************************************************************************* */
    public function delete_unit(Request $request)
    {
        $user_id = Auth::id();
        $unit = Unit::find($request->unit_id);
        $subject_id = $unit->subject_id;
        $SubjectTeacher = TeacherSubjectYear::where('user_id', $user_id)
                                        ->where('subject_id', $subject_id)
                                        ->first();

     if (!$SubjectTeacher) {
        return response()->json([
            'message' => 'you cannot delete this unit.',
        ], 404);
     }

     if ($unit) {
         $unit->update(['exist' => false]);
         Lesson::where('unit_id', $unit->id)
               ->update(['exist' => false]);

         return response()->json(['message' => 'Unit and related lessons have been deleted successfuly.']);
     } else {
         return response()->json(['message' => 'Unit not found.'], 404);
     }
    }
}
//******************************************************************************************************************************************* */
