<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\returnArgument;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class handleFileController extends Controller
{
    public function store(Request $request)
    {
        // validation data from frontend
        $validated = $request->validate([
            'formFile' => 'required|mimes:jpg,jpeg,jpe|',
            'formSize' => 'required|integer|min:50|max:1000'
        ]);

        // dd($validated['formFile']->getSize());

        //get the extension of image
        $getExtension = $request->file('formFile')->getClientOriginalExtension();

        // rename image
        $renameImage = 'original' . '_' . time() . '.' . $getExtension;

        // save the image to the directory application
        $path = $validated['formFile']->storeAs('original', $renameImage, 'public');

        // get the path of image in application directory
        $absolutePath = storage_path('app/public/' . $path);

        // send the compressed order to the custom compressFile() function
        $compressedUrl = $this->compressFile($absolutePath, $validated['formSize']);

        return response()->json([
            'message' => 'Success',
            'original' => asset('storage/' . $path),
            'compressed' => $compressedUrl
        ]);
    }

    public function compressFile($filePath, $compressSize)
    {
        $manager = new ImageManager(new Driver());

        // read file from directory application
        $fileToCompress = $manager->read($filePath);

        $width = $fileToCompress->width();
        $height = $fileToCompress->height();
        $currentImageSize = filesize($filePath);

        // dd($width, $height, $currentImageSize);

        // compressing process
        $tempPath = storage_path('app/temp_compress.jpg');
        $quality = 100;
        $minQuality = 1;
        $step = 1000;

        $targetBytes = $compressSize * 1024;

        while ($targetBytes < $currentImageSize) {
            $fileToCompress->scale(height: $height - $step, width: $width - $step);
            $fileToCompress->save($tempPath, quality: $quality);
            $currentImageSize = filesize($tempPath);
        }

        // $downScaleImage = $fileToCompress->scale(height: 600);

        // $fileToCompress->save($)

        // Ulangi penurunan kualitas sampai ukuran mendekati target
        // while ($currentSizeInBytes > $targetBytes && $quality >= $minQuality) {
        //     $fileToCompress->save($tempPath, quality: $quality);
        //     $currentSizeInBytes = filesize($tempPath);
        //     $quality -= $step;
        // }

        //rename image
        $fileName = 'compressed' . '_' . time() . '.jpg';

        // get the path of image in application directory
        $savePath = storage_path('/app/public/compressed/' . $fileName);

        // copy the final image compress that in temporary directory to permanent directory
        // copy($tempPath, $savePath);
        $fileToCompress->save($savePath, quality: 100);

        // delete all loop temporary image
        unlink($tempPath);

        return asset('storage/compressed/' . $fileName);
    }
}