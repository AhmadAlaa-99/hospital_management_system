<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait UploadTrait
{
    public function verifyAndStoreImage(Request $request, $inputname, $foldername, $disk, $imageable_id, $imageable_type)
    {
        if (!$request->hasFile($inputname)) {
            return null;
        }

        if (!$request->file($inputname)->isValid()) {
            throw new \RuntimeException('ملف الصورة غير صالح.');
        }

        $photo = $request->file($inputname);
        $name = \Str::slug($request->input('name') ?: 'upload');
        $filename = $name . '.' . $photo->getClientOriginalExtension();
        $targetDir = public_path('Dashboard/img/' . trim($foldername, '/'));

        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0775, true);
        }

        if (!is_writable($targetDir)) {
            throw new \RuntimeException('مجلد الصور غير قابل للكتابة. نفّذ: chmod -R 775 public/Dashboard/img');
        }

        $Image = new Image();
        $Image->filename = $filename;
        $Image->imageable_id = $imageable_id;
        $Image->imageable_type = $imageable_type;
        $Image->save();

        $photo->move($targetDir, $filename);

        return $filename;
    }

    public function verifyAndStoreImageForeach($varforeach, $foldername, $disk, $imageable_id, $imageable_type)
    {
        $targetDir = public_path('Dashboard/img/' . trim($foldername, '/'));
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0775, true);
        }

        $Image = new Image();
        $Image->filename = $varforeach->getClientOriginalName();
        $Image->imageable_id = $imageable_id;
        $Image->imageable_type = $imageable_type;
        $Image->save();

        $varforeach->move($targetDir, $varforeach->getClientOriginalName());

        return $varforeach->getClientOriginalName();
    }

    public function Delete_attachment($disk, $path, $id)
    {
        $fullPath = public_path('Dashboard/img/' . ltrim($path, '/'));
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }

        Image::where('imageable_id', $id)->delete();
    }
}
