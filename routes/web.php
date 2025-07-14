<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\handleFileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('homepage', function(){
    return view('homePage.index');
})->name('homepage');

Route::post('fileupload', [handleFileController::class, 'store'])->name('file.store');