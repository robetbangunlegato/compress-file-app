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
        // $renameImage = 'original' . '_' . time() . '.' . $getExtension;
        $renameImage = 'original.jpg';

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

        // ambil ukuran gambar asli
        $originalImage = $manager->read($filePath);
        $originalWidth = $originalImage->width();
        $originalHeight = $originalImage->height();

        $targetBytes = $compressSize * 1024;
        $tempPath = storage_path('app/temp_compress.jpg');

        $quality = 90;
        $minQuality = 30;
        $stepQuality = 5;

        $scaleFactor = 1.0; // 100%
        $minScale = 0.1;    // Jangan kurang dari 10%
        $stepScale = 0.05;  // Turunkan 5% per iterasi

        $iteration = 0;
        $currentImageSize = filesize($filePath);

        while ($currentImageSize > $targetBytes && $quality >= $minQuality && $scaleFactor > $minScale) {

            // Selalu mulai dari file asli
            $image = $manager->read($filePath);

            // Hitung ukuran baru berdasarkan scale factor
            $newWidth = intval($originalWidth * $scaleFactor);
            $newHeight = intval($originalHeight * $scaleFactor);

            // Resize proporsional
            $image->scale(width: $newWidth, height: $newHeight);

            // Simpan dengan kualitas saat ini
            $image->save($tempPath, quality: $quality);

            // Cek ukuran file hasil kompres
            $currentImageSize = filesize($tempPath);

            // Turunkan scale dan quality untuk iterasi berikutnya
            $scaleFactor -= $stepScale;
            $quality -= $stepQuality;
            $iteration++;

            // Debug jika ingin tahu progres
            if ($iteration == 8) {
                dd("iteration: $iteration | size: " . ($currentImageSize / 1024) . " KB | quality: $quality | scale: $scaleFactor" . " | Target: " . $targetBytes);
            }

            // Cegah loop tanpa akhir
            if ($iteration > 20) {
                break;
            }
        }

        // Simpan hasil akhir
        $fileName = 'compressed.jpg';
        $savePath = storage_path('/app/public/compressed/' . $fileName);
        copy($tempPath, $savePath);
        unlink($tempPath);

        return asset('storage/compressed/' . $fileName);
    }


    // public function compressFile($filePath, $compressSize)
    // {
    //     $manager = new ImageManager(new Driver());

    //     // read file from directory application
    //     $fileToCompress = $manager->read($filePath);

    //     $width = $fileToCompress->width();
    //     $height = $fileToCompress->height(); // 3541
    //     $currentImageSize = filesize($filePath);

    //     $targetBytes = $compressSize * 1024;

    //     // $length = strlen($width);
    //     $minus = null;

    //     if ($height >= 1000 && $height <= 2000) {
    //         $minus = 100; // true
    //     } elseif ($height > 1000) {
    //         $minus = 1000;
    //     } elseif ($height >= 100 && $height < 1000) {
    //         $minus = 50;
    //     }

    //     // compressing process
    //     $tempPath = storage_path('app/temp_compress.jpg');
    //     $quality = 90;
    //     $minQuality = 30;
    //     $stepQuality = 5;


    //     $itteration = 0;
    //     while ($currentImageSize >= $targetBytes && $quality >= $minQuality) {
    //         $fileToCompress = $manager->read($filePath);

    //         // scale & quality
    //         $fileToCompress->scale(height: $height -= $minus);
    //         $fileToCompress->save($tempPath, quality: $quality);
    //         if ($height >= 1000 && $height <= 2000) {
    //             $minus = 100; // true
    //         } elseif ($height > 1000) {
    //             $minus = 1000;
    //         } elseif ($height >= 100 && $height < 1000) {
    //             $minus = 50;
    //         }
    //         $currentImageSize = filesize($tempPath);
    //         $quality -= $stepQuality;
    //         $itteration++;
    //         if ($itteration > 20) {
    //             break;
    //         }
    //         if ($itteration == 1) {
    //             dd('itteration : ' . $itteration, 'height : ' . $height, 'quality : ' . $quality, 'size : ' . $currentImageSize / 1024, 'target size : ' . $targetBytes / 1024);
    //         }
    //     }

    //     //rename image
    //     // $fileName = 'compressed' . '_' . time() . '.jpg';
    //     $fileName = 'compressed.jpg';

    //     // get the path of image in application directory
    //     $savePath = storage_path('/app/public/compressed/' . $fileName);



    //     // copy the final image compress that in temporary directory to permanent directory
    //     copy($tempPath, $savePath);


    //     // delete all loop temporary image
    //     unlink($tempPath);

    //     // dd('iteration : ' . $itteration, 'height : ' . $height);


    //     return asset('storage/compressed/' . $fileName);
    // }
}