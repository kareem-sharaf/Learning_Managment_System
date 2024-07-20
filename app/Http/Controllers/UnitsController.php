<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Subject;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UnitsController extends Controller
{
    //******************************************************************************************* */
    public function show_all_units(Request $request)
    {
        $subject_id = $request->query('subject_id');
        $input= $request->all();
        $unit = Unit::where('subject_id', $subject_id)->with('lessons','files','videos')->get();
        $message = "this is the all units";
        return response()->json([
            'message' => $message,
            'data' => $unit,
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
        $user = auth()->user();
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
        'video_id' => $request->video_id,
        'file_id' => $request->file_id,
        'subject_id' => $request->subject_id,
    ]);
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
    $user = auth()->user();
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

    $unitData = $request->only(['name', 'description', 'video_id', 'file_id']);

    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($unit->image_url) {
            $oldImagePath = str_replace('/storage', 'public', $unit->image_url);
            \Log::info('Old image path: ' . $oldImagePath); // تسجيل مسار الصورة القديمة
            if (Storage::exists($oldImagePath)) {
                Storage::delete($oldImagePath);
                \Log::info('Old image deleted: ' . $oldImagePath); // تأكيد حذف الصورة القديمة
            } else {
                \Log::warning('Old image not found: ' . $oldImagePath); // تحذير إذا لم تُجد الصورة القديمة
            }
        }

        // Store new image
        $imagePath = $request->file('image')->store('unit_images', 'public');
        \Log::info('New image stored at: ' . $imagePath); // تسجيل مسار الصورة الجديدة
        $unitData['image_url'] = Storage::url($imagePath);
    }

    $unit->update($unitData);

    $message = "The unit edited successfully.";
    return response()->json([
        'message' => $message,
        'data' => $unit
    ]);
}

//********************************************************************************************************************************************* */
    public function delete_unit($unit_id)
    {
        $user = auth()->user();
        $unit = Unit::where('id', $unit_id)->first();
        if (is_null($unit)) {
            $message = "The unit doesn't exist.";
            return response()->json([
                'message' => $message,
            ]);
        }
        $unit->delete();
        if ($unit->image_url) {
            $oldImagePath = str_replace('/storage', 'public', $unit->image_url);
            if (Storage::exists($oldImagePath)) {
                Storage::delete($oldImagePath);
            }
        }
        $message = "The unit deleted successfully.";
        return response()->json([
            'message' => $message,
            'data' => $unit,
        ]);
    }
}
//******************************************************************************************************************************************* */
