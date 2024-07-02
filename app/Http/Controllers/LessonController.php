<?php

namespace App\Http\Controllers;

use Alaouy\Youtube\Facades\Youtube;
use App\Models\Files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Lesson;
use App\Models\Video;


class LessonController extends Controller
{
    public function add_lesson(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
              ]);
    
        $imagePath = $request->image->store('images', 'public');
        $imageFilename = basename($imagePath);
    
        $lesson = new Lesson();
        $lesson->name = $request->name;
        $lesson->price = $request->price;
        $lesson->description = $request->description;
        $lesson->unit_id = $request->unit_id;
        $lesson->image = $imagePath;
    
        if ($lesson->save()) {
            
            return response()->json([
                'message' => 'Lesson created successfully',
                'data' => $lesson,
                'status' => 200,
            ]);
        } else {
            return response()->json([
                'message' => 'Lesson not created',
                'status' => 400,
            ]);
        }
    }

    public function update_lesson(Request $request)
{

    
    $request->validate([
        'name'=>'required|string|max:255',
        'unit_id'=>'required',
        'price'=>'required|numeric|min:0',
        'description'=>'required|string|max:255',
        'image' => 'image|mimes:jpeg,png,jpg,gif|max:10240', 
        
    ]);
     $id=$request->id;
    $lesson = Lesson::findOrFail($id);

    $imagePath = $request->image ? $request->image->store('images', 'public') : $lesson->image;
    $imageFilename = basename($imagePath);

   
    $lesson->name = $request->name;
    $lesson->price = $request->price;
    $lesson->description = $request->description;
    $lesson->unit_id = $request->unit_id;

    $lesson->image = $imagePath;

    if ($lesson->save()) {
        return response()->json([
            'message' => 'Lesson updated successfully',
            'data' => $lesson,
            'status' => 200,
        ]);
    } else {
        return response()->json([
            'message' => 'Lesson not updated',
            'status' => 400,
        ]);
    }
}
public function delete_lesson(Request $request)
{
    
    $id = $request->id;
    $lesson = Lesson::findOrFail($id);

  
    Storage::delete([
        $lesson->image,
       
    ]);

    // Delete the lesson
    if ($lesson->delete()) {
        return response()->json([
            'message' => 'Lesson deleted successfully',
            'status' => 200,
        ]);
    } else {
        return response()->json([
            'message' => 'Lesson not deleted',
            'status' => 400,
        ]);
    }
} 

public function getLessonsByUnitId(Request $request)
{
    $request->validate([
        'unit_id' => 'required|integer|exists:units,id',
    ]);
    
    $unitId = $request->unit_id;

    $lessons = Lesson::where('unit_id', $unitId)->get();

    if ($lessons) {
        return response()->json([
            'message' => 'Lessons retrieved successfully',
            'data' => $lessons,
            'tatus' => 200,
        ]);
    } else {
        return response()->json([
            'message' => 'No lessons found for this unit',
            'status' => 404,
        ]);
    }
}

}