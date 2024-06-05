<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Files;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'content' => 'required|file|mimes:pdf,docx,xlsx,jpg,png,gif|max:10240', // 10MB max file size
        'subject_id' => 'required|integer|exists:subjects,id',
        'unit_id' => 'required|integer|exists:units,id',
        'lesson_id' => 'required|integer|exists:lessons,id',
    ]);

    $file = new Files();
    $file->name = $validatedData['name'];
    $file->subject_id = $validatedData['subject_id'];
    $file->unit_id = $validatedData['unit_id'];
    $file->lesson_id = $validatedData['lesson_id'];

    $fileContent = $request->file('content');
    $filePath = $fileContent->store('files', 'public');
    $file->content = $filePath;

    $file->save();

    return response()->json($file, 201);
}
public function update(Request $request)
{
    $validatedData = $request->validate([
        'id' => 'required|integer|exists:files,id'
    ]);

    $file = Files::find($validatedData['id']);

    if (!$file) {
        return response()->json(['error' => 'File not found'], 404);
    }

    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'content' => 'nullable|file|mimes:pdf,docx,xlsx,jpg,png,gif|max:10240', // 10MB max file size
        'subject_id' => 'required|integer|exists:subjects,id',
        'unit_id' => 'required|integer|exists:units,id',
        'lesson_id' => 'required|integer|exists:lessons,id',
    ]);

    if ($request->hasFile('content')) {
        Storage::delete($file->content);
        $fileContent = $request->file('content');
        $filePath = $fileContent->store('files', 'public');
        $file->content = $filePath;
    }

    $file->name = $validatedData['name'];
    $file->subject_id = $validatedData['subject_id'];
    $file->unit_id = $validatedData['unit_id'];
    $file->lesson_id = $validatedData['lesson_id'];

    $file->save();

    return response()->json($file, 200);
}

public function destroy(Request $request)
{
    $validatedData = $request->validate([
        'id' => 'required|integer|exists:files,id'
    ]);

    $file = Files::find($validatedData['id']);

    if (!$file) {
        return response()->json(['error' => 'File not found'], 404);
    }

    Storage::delete($file->content);

    $file->delete();

    return response()->json(['message' => 'File deleted successfully'], 200);
}


}
