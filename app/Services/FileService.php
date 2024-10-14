<?php

namespace App\Services;

use App\Models\File;

class FileService
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

    public function replaceFile($newFile, $type, $fileName)
    {
        $files = File::where('type_id', $type->id)->get();

        foreach ($files as $file) {
            $oldFilePath = public_path('files/' . basename($file->file));
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            $file->delete();
        }

        return $this->saveFile($newFile, $type, $fileName);
    }

    public function saveFile($file, $type, $fileName)
    {
        $fileDetails = $this->uploadFile($file,$fileName);

        $newFile = new File();
        $newFile->file = $fileDetails['path'];
        $newFile->name = $fileName;
        $newFile->type_id = $type->id;
        $newFile->type_type = get_class($type);
        $newFile->exist =true;

        $newFile->save();

        return $newFile;
    }


    public function deleteFiles($type_id)
    {
        File::where('type_id',$type_id)
            ->update(['exist' => false]);
    }
}
