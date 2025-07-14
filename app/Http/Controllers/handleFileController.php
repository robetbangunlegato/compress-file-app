<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class handleFileController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $validated = $request->validate([
            'formFile' => 'required|mimes:jpg,jpeg,jpe,pdf|',
            'formSize' => 'required|integer|min:50|max:1000'
        ]);

        // dd('fuck you');

        // return redirect('/homepage');

        //
    }

    public function compressFile(){
        
    }

}