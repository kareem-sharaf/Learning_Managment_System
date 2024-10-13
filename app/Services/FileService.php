<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class

FileService
{
    public function uploadFile($file, $fileName)
    {
        $fileNameWithExtension = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('files'), $fileNameWithExtension);

        return [
            'path' => url('files/' . $fileNameWithExtension),
            'name' => $fileName
        ];
    }

    public function replaceFile($newFile, $oldFilePath, $fileName)
    {
        if ($oldFilePath && file_exists(public_path('files/' . basename($oldFilePath)))) {
            unlink(public_path('files/' . basename($oldFilePath)));
        }

        return $this->uploadFile($newFile, $fileName);
    }
}
