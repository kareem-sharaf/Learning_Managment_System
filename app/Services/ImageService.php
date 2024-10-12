<?php

namespace App\Services;

class ImageService
{
    public function uploadImage($image, $folder)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path($folder), $imageName);
        return url($folder . '/' . $imageName);
    }

    public function replaceImage($newImage, $oldImagePath, $folder)
    {
        // Delete old image if it exists
        if ($oldImagePath && file_exists(public_path($folder . '/' . basename($oldImagePath)))) {
            unlink(public_path($folder . '/' . basename($oldImagePath)));
        }

        // Upload new image
        return $this->uploadImage($newImage, $folder);
    }
}
