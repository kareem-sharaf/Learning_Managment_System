<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Files;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FilesController extends Controller
{
    public function store(Request $request)
    {
<<<<<<< HEAD
        $sender = Auth::user();
        $sender_role_id = $sender->role_id;

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|file|mimes:pdf,docx',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'lesson_id' => 'nullable|integer|exists:lessons,id',
        ]);

        $count = collect([$validatedData['subject_id'], $validatedData['unit_id'], $validatedData['lesson_id']])
            ->filter()
            ->count();

        if ($count > 1) {
            return response()->json(['error' => 'Only one of subject_id, unit_id, or lesson_id must be selected'], 422);
        }

        if (($sender_role_id == '2' || $sender_role_id == '3'||$sender_role_id == '1') ) {

            $fileContent = $request->file('content');
            $filePath = $fileContent->storeAs('files', $fileContent->getClientOriginalName(), 'public');

            $file = Files::create([
                'name' => $validatedData['name'],
                'content' => $filePath,
                'subject_id' => $validatedData['subject_id'] ?? null,
                'unit_id' => $validatedData['unit_id'] ?? null,
                'lesson_id' => $validatedData['lesson_id'] ?? null,
            ]);

            $fileUrl = Storage::url($filePath);

            return response()->json([
                'file' => $file,
                'file_url' => $fileUrl
            ], 201);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function update(Request $request)
    {
        $sender = Auth::user();
        $sender_role_id = $sender->role_id;

        $validatedData = $request->validate([
            'id' => 'required|integer|exists:files,id'
        ]);

        $file = Files::find($validatedData['id']);

        if (!$file) {
            return response()->json(['error' => 'File not found'], 404);
        }

        if (($sender_role_id == '2' || $sender_role_id == '3'||$sender_role_id == '1') ) {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'content' => 'nullable|file|mimes:pdf,docx',
                'subject_id' => 'nullable|integer|exists:subjects,id',
                'unit_id' => 'nullable|integer|exists:units,id',
                'lesson_id' => 'nullable|integer|exists:lessons,id',
            ]);

            $count = collect([$validatedData['subject_id'], $validatedData['unit_id'], $validatedData['lesson_id']])
                ->filter()
                ->count();

            if ($count > 1) {
                return response()->json(['error' => 'Only one of subject_id, unit_id, or lesson_id must be selected'], 422);
            }

            if ($request->hasFile('content')) {
                Storage::delete($file->content);
                $fileContent = $request->file('content');
                $filePath = $fileContent->storeAs('files', $fileContent->getClientOriginalName(), 'public');
                $file->content = $filePath;
            }

            $file->name = $validatedData['name'];
            $file->subject_id = $validatedData['subject_id'] ?? $file->subject_id;
            $file->unit_id = $validatedData['unit_id'] ?? $file->unit_id;
            $file->lesson_id = $validatedData['lesson_id'] ?? $file->lesson_id;

            $file->save();

            return response()->json($file, 200);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function destroy(Request $request)
    {
        $sender = Auth::user();
        $sender_role_id = $sender->role_id;

        $validatedData = $request->validate([
            'id' => 'required|integer|exists:files,id'
        ]);

        if (($sender_role_id == '2' || $sender_role_id == '3'||$sender_role_id == '1') ) {
=======
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|file|mimes:pdf,docx',
            'subject_id' => 'required|integer|exists:subjects,id',
            'unit_id' => 'required|integer|exists:units,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
        ]);
        $sender = Auth::user();
        $sender_role_id= $sender->role_id;

        if (($sender_role_id == '2' || $sender_role_id == '3'|| $sender_role_id='1') ) {

        $file = new Files();
        $file->name = $validatedData['name'];
        $file->subject_id = $validatedData['subject_id'];
        $file->unit_id = $validatedData['unit_id'];
        $file->lesson_id = $validatedData['lesson_id'];

        $fileContent = $request->file('content');
        $filePath = $fileContent->store('videos', 'public');

        $file->content = $filePath;

        $file->save();
        }
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
            'content' => 'nullable|file|mimes:pdf,docx',
            'subject_id' => 'required|integer|exists:subjects,id',
            'unit_id' => 'required|integer|exists:units,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
        ]);

        $sender = Auth::user();
        $sender_role_id= $sender->role_id;
        if (($sender_role_id == '2' || $sender_role_id == '3'|| $sender_role_id='1') ) {
        if ($request->hasFile('content')) {
            Storage::delete($file->content);
            $fileContent = $request->file('content');
        $filePath = $fileContent->store('videos', 'public');

            $file->content = $filePath;
        }

        $file->name = $validatedData['name'];
        $file->subject_id = $validatedData['subject_id'];
        $file->unit_id = $validatedData['unit_id'];
        $file->lesson_id = $validatedData['lesson_id'];

        $file->save();

        return response()->json($file, 200);
    }}

>>>>>>> 4daf2304093ea2d807c7f190a68be2a2151cc98b

            $file = Files::find($validatedData['id']);

            if (!$file) {
                return response()->json(['error' => 'File not found'], 404);
            }

            Storage::delete($file->content);

            $file->delete();

            return response()->json(['message' => 'File deleted successfully'], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
<<<<<<< HEAD
=======
    $sender = Auth::user();
    $sender_role_id= $sender->role_id;
    if (($sender_role_id == '2' || $sender_role_id == '3'|| $sender_role_id='1') ) {

    Storage::delete($file->content);

    $file->delete();

    return response()->json(['message' => 'File deleted successfully'], 200);
}}


>>>>>>> 4daf2304093ea2d807c7f190a68be2a2151cc98b
}
