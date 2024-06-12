<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
            use Illuminate\Support\Facades\Artisan;

trait Upload
{
    public function UploadFile(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        
        try {
            // Create symbolic link for storage
            Artisan::call('storage:link');
            // echo 'Symlink process successfully completed';
        } catch (\Exception $e) {
            // Handle any exceptions
            echo 'An error occurred: ' . $e->getMessage();
        }

        $FileName = !is_null($filename) ? $filename : Str::random(10);
        return $file->storeAs(
            $folder,
            $FileName . "." . $file->getClientOriginalExtension(),
            $disk
        );
    }

    public function deleteFile($path, $disk = 'public')
    {
        Storage::disk($disk)->delete($path);
    }
}
