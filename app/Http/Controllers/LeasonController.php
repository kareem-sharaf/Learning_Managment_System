<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Leason;


class LeasonController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', 
        ]);
    
        $imagePath = $request->image->store('images', 'public');
        $imageFilename = basename($imagePath);
    
        $image = new Leason();
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
        $image=Leason::orderBy('id','desc')->get();
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
        $image=Leason::findOrFail($request->id); 
    
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
    
        $images=Leason::find($request->id);
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
    public function uploadvedio(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'video' => 'required|file|mimetypes:video/x-msvideo,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-ms-wmv,video/x-ms-asf,video/x-flv,video/MP2T,video/3gpp,video/quicktime,video/x-ms-wmv,video/x-ms-asf,video/x-matroska,video/webm|max:10240',
        ]);
        $videoPath = $request->video->store('videos', 'public');
        $videoFilename = basename($videoPath);
    
        $video = new Leason();
        $video->title = $request->title;
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
            'title' => 'required',
            'video' => 'file|mimetypes:video/x-msvideo,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-ms-wmv,video/x-ms-asf,video/x-matroska,video/webm|max:10240',
        ]);
        $video=Leason::findOrFail($request->id); 
    
        if ($request->hasFile('video')) {
            // Delete the old video file
            Storage::disk('public')->delete($video->video);
    
            // Store the new video file
            $videoPath = $request->video->store('videos', 'public');
            $video->video = $videoPath;
        }
    
        $video->title = $request->title;
        $video->save();
    
        return response()->json([
            'message' => 'Video updated successfully',
            'video' => $video,
        ]);
    }
    
    public function deletevedio(Request $request){
    
        $vedio=Leason::find($request->id);
        $distination=public_path("storage\\".$vedio->video);
        if(File::exists($distination)){
            File::delete($distination);
        } 
        $r= $vedio->delete();
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
            'title' => 'required',
            'pdf' => 'required|file|mimetypes:application/pdf|max:10240', // Change the field name from 'video' to 'pdf' and add the mimetypes rule for PDF files
        ]);
    
        $pdfPath = $request->pdf->store('pdfs', 'public');
        $pdfFilename = basename($pdfPath);
    
        $pdf = new Leason();
        $pdf->title = $request->title;
        $pdf->pdf = $pdfPath;
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
            'title' => 'required',
            'pdf' => 'file|mimetypes:application/pdf|max:10240',
        ]);
        $pdf = Leason::findOrFail($request->id); 
    
        if ($request->hasFile('pdf')) {
            // Delete the old PDF file
            Storage::disk('public')->delete($pdf->pdf);
    
            // Store the new PDF file
            $pdfPath = $request->pdf->store('pdfs', 'public');
            $pdf->pdf = $pdfPath;
        }
    
        $pdf->title = $request->title;
        $pdf->save();
    
        return response()->json([
            'message' => 'PDF updated successfully',
            'pdf' => $pdf,
        ]);
    }
    function deletepdf(Request $request)
    {
            $pdf = Leason::findOrFail($request->id); 
    
    
        // Delete the PDF file
        Storage::disk('public')->delete($pdf->pdf);
    
        // Delete the PDF record
        $pdf->delete();
    
        return response()->json([
            'message' => 'PDF deleted successfully',
        ]);
    }
}
