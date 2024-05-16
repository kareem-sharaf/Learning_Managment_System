<?php

namespace App\Http\Controllers;

use Alaouy\Youtube\Facades\Youtube;
use App\Models\Files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Leeson;
use App\Models\Lesson;
use App\Models\Video;


class LeesonController extends Controller
{
    public function add_lesson(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'unit_id'=>'required',
            'price'=>'required|numeric|min:0',
            'description'=>'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', 
            'file' => 'required|file|mimetypes:application/pdf|max:10240',
            'video' => 'required|file|mimetypes:video/x-msvideo,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-ms-wmv,video/x-ms-asf,video/x-flv,video/MP2T,video/3gpp,video/quicktime,video/x-ms-wmv,video/x-ms-asf,video/x-matroska,video/webm|max:10240',
        ]);
     
        $imagePath = $request->image->store('images', 'public');
        $imageFilename = basename($imagePath);
    
        $videoPath = $request->video->store('videos', 'public');
        $videoFilename = basename($videoPath);
    
        $pdfPath = $request->file->store('pdfs', 'public');
    


          $lesson=new Lesson();
          $lesson->name=$request->name;
          $lesson->price=$request->price;
          $lesson->description=$request->description;
          $lesson->unit_id=$request->unit_id;
          $lesson->file=$pdfPath;
          $lesson->video = $videoPath;
          $lesson->image = $imagePath;
         if( $lesson->save()){
            return response()->json([
                'message' => 'Lesson created successfully',
                'data' => $lesson,
                'status'=>200,
            ]);}
         else{
            return response()->json([
                'message' => 'Lesson not created',
                'status'=>400,
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
        'file' => 'file|mimetypes:application/pdf|max:10240',
        'video' => 'file|mimetypes:video/x-msvideo,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-ms-wmv,video/x-ms-asf,video/x-flv,video/MP2T,video/3gpp,video/quicktime,video/x-ms-wmv,video/x-ms-asf,video/x-matroska,video/webm|max:10240',
    ]);
     $id=$request->id;
    $lesson = Lesson::findOrFail($id);

    $imagePath = $request->image ? $request->image->store('images', 'public') : $lesson->image;
    $imageFilename = basename($imagePath);

    $videoPath = $request->video ? $request->video->store('videos', 'public') : $lesson->video;
    $videoFilename = basename($videoPath);

    $pdfPath = $request->file ? $request->file->store('pdfs', 'public') : $lesson->file;

    $lesson->name = $request->name;
    $lesson->price = $request->price;
    $lesson->description = $request->description;
    $lesson->unit_id = $request->unit_id;
    $lesson->file = $pdfPath;
    $lesson->video = $videoPath;
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

    // Delete the image, video, and PDF files
    Storage::delete([
        $lesson->image,
        $lesson->video,
        $lesson->file,
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
public function get_all_lessons()
{
    // Retrieve all lessons from the database
    $lessons = Lesson::all();

    // Return a JSON response with all lessons
    return response()->json([
        'message' => 'All lessons retrieved successfully',
        'data' => $lessons,
        'status' => 200,
    ]);
}

}