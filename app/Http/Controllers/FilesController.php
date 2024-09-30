<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FilesController extends Controller
{
    public function store(Request $request)
    {
        $sender = Auth::user();
        $sender_role_id = $sender->role_id;

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,docx,xlsx|max:20480',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'lesson_id' => 'nullable|integer|exists:lessons,id',
        ]);



        if (in_array($sender_role_id, ['1', '2', '3'])) {

            $fileContent = $request->file('file');
            $fileName = time() . '.' . $fileContent->getClientOriginalExtension();
            $filePath = $fileContent->move(public_path('files'), $fileName);

            $file = File::create([
                'name' => $validatedData['name'],
                'file' => url('files/' . $fileName),
                'subject_id' => $validatedData['subject_id'] ?? null,
                'unit_id' => $validatedData['unit_id'] ?? null,
                'lesson_id' => $validatedData['lesson_id'] ?? null,
            ]);

            return response()->json([
                'file' => $file,
                'file_url' => url('files/' . $fileName)
            ], 201);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }



    public function update(Request $request)
{
    $sender = Auth::user();
    $sender_role_id = $sender->role_id;

    // تحقق من صلاحيات المستخدم بناءً على دوره
    if (in_array($sender_role_id, ['1', '2', '3'])) {
        // التحقق من المدخلات
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:files,id',
            'name' => 'required|string|max:255', // يمكن تعديل الاسم
            'file' => 'nullable|file|mimes:pdf,docx,xlsx|max:20480', // تحديث الملف إذا تم تحميله
        ]);

        // جلب الملف من قاعدة البيانات بناءً على المعرف
        $file = File::find($validatedData['id']);

        if (!$file) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // تحقق إذا تم تحميل ملف جديد
        if ($request->hasFile('file')) {
            // حذف الملف القديم من التخزين
            if ($file->file) {
                $oldFilePath = str_replace(url('/'), '', $file->file);
                if (file_exists(public_path($oldFilePath))) {
                    unlink(public_path($oldFilePath));
                }
            }

            // رفع الملف الجديد
            $fileContent = $request->file('file');
            $fileName = time() . '.' . $fileContent->getClientOriginalExtension();
            $filePath = $fileContent->move(public_path('files'), $fileName);

            $file->file = url('files/' . $fileName);
        }

        $file->name = $validatedData['name'];
        $file->save();

        return response()->json([
            'file' => $file,
            'file_url' => $file->file
        ], 200);
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

    if (in_array($sender_role_id, ['1', '2', '3'])) {

        $file = File::find($validatedData['id']);

        if (!$file) {
            return response()->json(['error' => 'File not found'], 404);
        }

        if ($file->file) {
            $filePath = str_replace(url('/'), '', $file->file);

            if (file_exists(public_path($filePath))) {
                unlink(public_path($filePath));
            }
        }

        $file->delete();

        return response()->json(['message' => 'File deleted successfully'], 200);
    }

    return response()->json(['error' => 'Unauthorized'], 403);
}

}
