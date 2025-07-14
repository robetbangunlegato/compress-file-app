<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;


class handleFileController extends Controller
{


    /** 
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'formFile' => 'required|mimes:jpg,jpeg,jpe,pdf|',
            'formSize' => 'required|integer|min:50|max:1000'
        ]);

        $file = $validated['formFile'];
        $extension = $file->getClientOriginalExtension();
        $filename = 'original_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $extension;
        $upload = $file->store($filename, 'public');
        return ('succsess to upload!');

        //
    }

    public function compressFile()
    {

    }

}