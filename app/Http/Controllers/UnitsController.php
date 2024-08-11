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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use Illuminate\Support\Facades\Auth;

class UnitsController extends Controller
{
    //******************************************************************************************* */
    public function show_all_units(Request $request)
    {
        $user=Auth::user();
        $user_id=$user->id;
        $role_id=$user->role_id;
        // $subject_id = $request->query('subject_id');

        $subject_id = $request->subject_id;

        $input= $request->all();
        $unit = Unit::where('subject_id', $subject_id)->with('lessons','lessons.videos','files','lessons.files','videos')->get();
        // $subject_id = $unit->subject_id;
        if($role_id==4 ){
        $isSubscription = Subscription::where('user_id',$user_id)
                                        ->where('subject_id',$subject_id)
                                        ->first();
        if($isSubscription){
            $Subscriped=true;
        }else{
            $Subscriped=false;
        }

        $message = "this is the all units";
        return response()->json([
            'isSubscription' => $Subscriped,
            'message' => $message,
            'data' => $unit
        ]);
    }elseif($role_id == 3){
        $Owner = TeacherSubjectYear::where('user_id',$user_id)
                                        ->where('subject_id',$subject_id)
                                        ->first();
        if($Owner){
            $isOwner=true;
        }else{
            $isOwner=false;
        }
        $message = "this is the all units";
        return response()->json([
            'isOwner' => $isOwner,
            'message' => $message,
            'data' => $unit
        ]);
    }else{
        $unit = Unit::where('subject_id', $subject_id)->with('lessons','lessons.videos','files','lessons.files','videos')->get();
        $message = "this is the all units.";
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $unit
        ]);
    }
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
        $user_id=$user->id;
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'required' ,
            'video_id' => 'integer',
            'file_id' => 'integer',
            'subject_id' => 'integer',
        ]);

        $subject = Subject::find($request->input('subject_id'));
     if (!$subject) {
        return response()->json(['message' => 'subject not found.'], 404);
     }
     $subject_id=$subject->id;
     $SubjectTeacher = TeacherSubjectYear::where('user_id', $user_id)
                                            ->where('subject_id', $subject_id)
                                            ->first();

        if (!$SubjectTeacher) {
        return response()->json([
        'message' => 'you can not add this unit.',
        ], 404);
        }

     // Check if image is uploaded
     if (!$request->hasFile('image')) {
        return response()->json(['message' => 'Image file is required.'], 400);
     }

     // Store the image and get the URL
     $imagePath = $request->file('image')->store('unit_images', 'public');
     $imageUrl = Storage::url($imagePath);

     $unit = Unit::create([
        'name' => $request->name,
        'description' => $request->description,
        'image_url' => $imageUrl,
        'video' => 'nullable|mimes:mp4,mov,avi,flv|max:204800', // Max 200MB for video
        'video_name' => 'nullable|string|max:255',
        'file_id' => $request->file_id,
        'subject_id' => $request->subject_id,
     ]);

     if ($unit->save()) {
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
        }
        $unit->load('videos');
        $unit->load('lessons');

        $message = "add unit successfully";
        return response()->json(
            [
                'message' => $message,
                'data' => $unit
            ]
        );
    }
//**************************************************************** */
public function edit_unit(Request $request)
{
    $user = Auth::user();
    $user_id = $user->id;
    $request->validate([
        'unit_id' => 'required|integer|exists:units,id',
        'name' => 'string|max:255|nullable',
        'image' => 'image|mimes:jpeg,png,jpg,gif|max:10240|nullable',
        'description' => 'string|nullable',
    ]);

    $unit = Unit::find($request->unit_id);
    if (!$unit) {
        return response()->json(['message' => 'unit not found'], 404);
    }
    $subject_id = $unit->subject_id;
    $SubjectTeacher = TeacherSubjectYear::where('user_id', $user_id)
                                        ->where('subject_id', $subject_id)
                                        ->first();

    if (!$SubjectTeacher) {
        return response()->json([
            'message' => 'you can not edit this unit.',
        ], 404);
    }

    $unitData = $request->only(['name', 'description', 'video_id', 'file_id']);

    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($unit->image_url) {
            $oldImagePath = str_replace('/storage', 'public', $unit->image_url);
            \Log::info('Old image path: ' . $oldImagePath);
            if (Storage::exists($oldImagePath)) {
                Storage::delete($oldImagePath);
                \Log::info('Old image deleted: ' . $oldImagePath);
            } else {
                \Log::warning('Old image not found: ' . $oldImagePath);
            }
        }

        // Store new image
        $imagePath = $request->file('image')->store('unit_images', 'public');
        \Log::info('New image stored at: ' . $imagePath);
        $unitData['image_url'] = Storage::url($imagePath);
    }

    if ($request->hasFile('video')) {
        $video_id = $unit->video_id;
        $video = Video::find($video_id);
        if ($video) {
            // Delete old video
            $oldVideoPath = str_replace('/storage', 'public', $video->video);
            if (Storage::exists($oldVideoPath)) {
                Storage::delete($oldVideoPath);
            }
        } else {
            // Create new video instance if it doesn't exist
            $video = new Video();
            $video->unit_id = $unit->id;
        }

        // Store new video
        $videoPath = $request->file('video')->store('videos', 'public');
        $video->video = Storage::url($videoPath);

        if ($request->filled('video_name')) {
            $video->name = $request->video_name;
        }

        $video->save();
        $unit->video_id = $video->id;
    }
    $unit->update($unitData);

    $message = "The unit edited successfully.";
    return response()->json([
        'message' => $message,
        'data' => $unit
    ]);
}

//********************************************************************************************************************************************* */
    public function delete_unit(Request $request)
    {
        $user_id = Auth::id();
        $unit = Unit::find($request->unit_id);
        if (!$unit) {
            $message = "The unit doesn't exist.";
            return response()->json([
                'message' => $message,
            ], 404);
        }
        $subject_id = $unit->subject_id;
        $SubjectTeacher = TeacherSubjectYear::where('user_id', $user_id)
                                        ->where('subject_id', $subject_id)
                                        ->first();

     if (!$SubjectTeacher) {
        return response()->json([
            'message' => 'you cannot delete this unit.',
        ], 404);
     }
        if ($unit->image_url) {
            $oldImagePath = str_replace('/storage', 'public', $unit->image_url);
            if (Storage::exists($oldImagePath)) {
                Storage::delete($oldImagePath);
            }
        }

        $video_id = $unit->video_id;
        $video = Video::find($video_id);
        if ($video) {
            // Delete old video
            $oldVideoPath = str_replace('/storage', 'public', $video->video);
            if (Storage::exists($oldVideoPath)) {
                Storage::delete($oldVideoPath);

            }
        }

        $video->delete();
        $unit->delete();

        $message = "The unit deleted successfully.";
        return response()->json([
            'message' => $message,
            'data' => $unit,
        ]);
    }
}
//******************************************************************************************************************************************* */
