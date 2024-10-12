<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileService
{
    // Method to replace the file
    public function replaceFile($newFile, $oldFilePath, $fileName)
    {
        // Delete old file if it exists
        if ($oldFilePath && Storage::disk('public')->exists(basename($oldFilePath))) {
            Storage::disk('public')->delete(basename($oldFilePath));
        }

        // Upload new file
        return $this->uploadFile($newFile, $fileName);
    }

    // Existing upload method
    public function uploadFile($file, $fileName)
    {
        $filePath = $file->store('files', 'public');
        return [
            'path' => Storage::url($filePath),
            'name' => $fileName,
        ];
    }
}
