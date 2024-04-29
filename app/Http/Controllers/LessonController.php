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



    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $imagePath = $request->image->store('images', 'public');
        $imageFilename = basename($imagePath);

        $image = new Lesson();
        $image->title = $request->title;
        $image->image = $imagePath;
        $image->save();

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image' => $image,
        ]);
    }
    /////////////////////////////////////////
    public function getall(){
        $image=Lesson::orderBy('id','desc')->get();
        return response()->json($image);
    }
    ///////////////////////////////////////////////////////
    public function update(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        $image=Lesson::findOrFail($request->id);

        if ($request->hasFile('image')) {
            // Delete the old image file
            Storage::disk('public')->delete($image->image);

            // Store the new image file
            $imagePath = $request->image->store('images', 'public');
            $image->image = $imagePath;
        }

        $image->title = $request->title;
        $image->save();

        return response()->json([
            'message' => 'Image updated successfully',
            'image' => $image,
        ]);
    }
    /////////////////////////////////////////////////////////////
    public function delete(Request $request){

        $images=Lesson::find($request->id);
        $distination=public_path("storage\\".$images->image);
        if(File::exists($distination)){
            File::delete($distination);
        }
        $r= $images->delete();
        if($r){
            return response()->json(['success'=>true]);

        }else{
            return response()->json(['success'=>false]);
        }
    }
    //////////////////////////////////////
    public function uploadvideo(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'unit_id'=>'required',
            'leeson_id'=>'required',
            'video' => 'required|file|mimetypes:video/x-msvideo,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-ms-wmv,video/x-ms-asf,video/x-flv,video/MP2T,video/3gpp,video/quicktime,video/x-ms-wmv,video/x-ms-asf,video/x-matroska,video/webm|max:10240',
        ]);
        $videoPath = $request->video->store('videos', 'public');
        $videoFilename = basename($videoPath);

        $video = new  Video();
        $video->name = $request->name;
        $video->unit_id = $request->unit_id;
        $video->leeson_id = $request->leeson_id;


        $video->video = $videoPath;
        $video->save();

        return response()->json([
            'message' => 'Video uploaded successfully',
            'video' => $video,
        ]);
    }
    public function updateVideo(Request $request)
    {


        $request->validate([
            'id'=>'required',
            'name' => 'required',
            'video' => 'file|mimetypes:video/x-msvideo,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-ms-wmv,video/x-ms-asf,video/x-matroska,video/webm|max:10240',
        ]);
        $video=Video::findOrFail($request->id);

        if ($request->hasFile('video')) {
            // Delete the old video file
            Storage::disk('public')->delete($video->video);

            // Store the new video file
            $videoPath = $request->video->store('videos', 'public');
            $video->video = $videoPath;
        }

        $video->name = $request->name;
        $video->save();

        return response()->json([
            'message' => 'Video updated successfully',
            'video' => $video,
        ]);
    }

    public function deletevideo(Request $request){

        $Video=Video::find($request->id);
        $distination=public_path("storage\\".$Video->video);
        if(File::exists($distination)){
            File::delete($distination);
        }
        $r= $Video->delete();
        if($r){
            return response()->json(['success'=>true]);

        }else{
            return response()->json(['success'=>false]);
        }
    }
    /////////////////////////////////////////////////////////////////////
    function uploadpdf(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'file' => 'required|file|mimetypes:application/pdf|max:10240', // Change the field name from 'video' to 'pdf' and add the mimetypes rule for PDF files
            'unit_id'=>'required',
            'leeson_id'=>'required'
        ]);

        $pdfPath = $request->file->store('pdfs', 'public');

        $pdf = new Files();
        $pdf->name = $request->name;
        $pdf->unit_id=$request->unit_id;
        $pdf->leeson_id=$request->leeson_id;
        $pdf->file = $pdfPath;
        $pdf->save();

        return response()->json([
            'message' => 'PDF uploaded successfully',
            'pdf' => $pdf,
        ]);
    }
    ////////////////////////////////////////////////
    function updatepdf(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'name' => 'required',
            'file' => 'file|mimetypes:application/pdf|max:10240',
        ]);
        $pdf = Files::findOrFail($request->id);

        if ($request->hasFile('pdf')) {
            // Delete the old PDF file
            Storage::disk('public')->delete($pdf->file);

            // Store the new PDF file
            $pdfPath = $request->file->store('pdfs', 'public');
            $pdf->file = $pdfPath;
        }

        $pdf->name = $request->name;
        $pdf->save();

        return response()->json([
            'message' => 'PDF updated successfully',
            'pdf' => $pdf,
        ]);
    }
    function deletepdf(Request $request)
    {
            $pdf = Files::findOrFail($request->id);


        // Delete the PDF file
        Storage::disk('public')->delete($pdf->file);

        // Delete the PDF record
        $pdf->delete();

        return response()->json([
            'message' => 'PDF deleted successfully',
        ]);
    }

}
